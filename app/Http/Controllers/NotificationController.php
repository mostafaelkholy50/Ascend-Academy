<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Exception;

class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        try {
            $data = $this->service->getIndexData(auth()->user());
            return view('notifications.index', $data);
        } catch (Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحميل الإشعارات.');
        }
    }

    /**
     * Get unread notifications for header dropdown (AJAX)
     */
    public function getUnread()
    {
        try {
            $data = $this->service->getUnreadData(auth()->user());
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to load notifications'], 500);
        }
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        try {
            $this->service->markAsRead(auth()->user(), $id);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to mark as read'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $this->service->markAllAsRead(auth()->user());
            return $this->successResponse('All notifications marked as read.');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to mark notifications as read.');
        }
    }
}
