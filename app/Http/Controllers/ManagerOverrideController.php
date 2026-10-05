<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ManagerOverrideController extends Controller
{
    public function show(Request $request): View
    {
        $user = Auth::user();
        $targetUrl = $request->query('intended', route('dashboard'));
        $branch = $user->branch ?? Branch::where('is_main', true)->first();

        return view('auth.manager-override', compact('user', 'targetUrl', 'branch'));
    }

    public function authorizeOverride(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manager_email' => 'nullable|string|max:255',
            'manager_password' => 'required|string',
            'intended_url' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $branchId = $currentUser->branch_id;

        // Find eligible branch managers or super admins
        $query = User::where('status', 'active')
            ->whereHas('role', function ($q) {
                $q->whereIn('slug', ['admin', 'super-admin']);
            });

        if (!empty($validated['manager_email'])) {
            $query->where('email', $validated['manager_email']);
        } else {
            // Find managers matching this branch or super-admins
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('role', function ($r) {
                      $r->where('slug', 'super-admin');
                  });
            });
        }

        $managers = $query->get();
        $authorizedManager = null;

        foreach ($managers as $mgr) {
            if (Hash::check($validated['manager_password'], $mgr->password)) {
                $authorizedManager = $mgr;
                break;
            }
        }

        if (!$authorizedManager) {
            return back()->with('error', 'Invalid Branch Manager password or manager not authorized for this branch.')->withInput();
        }

        // Grant 15 minutes of manager override in session
        session([
            'manager_authorized_until' => now()->addMinutes(15)->timestamp,
            'authorized_by_manager_id' => $authorizedManager->id,
            'authorized_by_manager_name' => $authorizedManager->name,
        ]);

        AuditService::log(
            'manager_override_granted',
            'Security',
            (string) $currentUser->id,
            null,
            [
                'cashier_id' => $currentUser->id,
                'cashier_name' => $currentUser->name,
                'manager_id' => $authorizedManager->id,
                'manager_name' => $authorizedManager->name,
                'branch_id' => $branchId,
            ]
        );

        $target = $validated['intended_url'] ?: route('dashboard');
        return redirect($target)->with('success', "Manager override granted by {$authorizedManager->name} (Valid for 15 minutes).");
    }

    public function ajaxAuthorize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string',
            'email' => 'nullable|string',
            'action' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $branchId = $currentUser->branch_id;

        $query = User::where('status', 'active')
            ->whereHas('role', function ($q) {
                $q->whereIn('slug', ['admin', 'super-admin']);
            });

        if (!empty($validated['email'])) {
            $query->where('email', $validated['email']);
        } else {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('role', function ($r) {
                      $r->where('slug', 'super-admin');
                  });
            });
        }

        $managers = $query->get();
        $authorizedManager = null;

        foreach ($managers as $mgr) {
            if (Hash::check($validated['password'], $mgr->password)) {
                $authorizedManager = $mgr;
                break;
            }
        }

        if (!$authorizedManager) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Branch Manager password or manager is not authorized for this branch.',
            ], 422);
        }

        AuditService::log(
            'pos_manager_approval',
            'POS',
            (string) $currentUser->id,
            null,
            [
                'action' => $validated['action'] ?? 'manager_override',
                'cashier_id' => $currentUser->id,
                'manager_id' => $authorizedManager->id,
                'manager_name' => $authorizedManager->name,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Authorized by Manager {$authorizedManager->name}.",
            'manager_name' => $authorizedManager->name,
        ]);
    }

    public function revokeOverride(Request $request): RedirectResponse
    {
        session()->forget(['manager_authorized_until', 'authorized_by_manager_id', 'authorized_by_manager_name']);
        return redirect()->route('pos.index')->with('info', 'Manager override session closed.');
    }
}

