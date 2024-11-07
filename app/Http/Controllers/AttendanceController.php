<?php

namespace App\Http\Controllers;

use Laradevsbd\Zkteco\Http\Library\ZKTecoLib;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $zk;

    public function __construct()
    {
        $this->zk = new ZKTecoLib(env('ZKTECO_DEVICE_IP'), env('ZKTECO_DEVICE_PORT'));
    }

    public function connect()
    {
        // Connect to the device
        if ($this->zk->connect()) {
            return response()->json(['status' => 'Connected to ZKTeco device']);
        } else {
            return response()->json(['status' => 'Failed to connect to ZKTeco device'], 500);
        }
    }

    public function getAttendanceLogs()
    {
        // Ensure device is connected
        if ($this->zk->connect()) {
            // Get attendance data
            $attendanceLogs = $this->zk->getAttendance();
            $this->zk->disconnect();

            return response()->json($attendanceLogs);
        } else {
            return response()->json(['status' => 'Could not connect to the device'], 500);
        }
    }
}
