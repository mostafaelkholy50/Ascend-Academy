<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function successResponse($message)
    {
        return back()->with('success', $message);
    }

    protected function errorResponse($message)
    {
        return back()->with('error', $message);
    }
}
