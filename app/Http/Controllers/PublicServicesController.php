<?php

namespace App\Http\Controllers;

class PublicServicesController extends Controller
{
    public function teleconsultation()
    {
        return view('services.teleconsultation');
    }

    public function appointment()
    {
        return view('services.appointment');
    }

    public function pharmacy()
    {
        return view('services.pharmacy');
    }

    public function insurance()
    {
        return view('services.insurance');
    }

    public function emergency()
    {
        return view('services.emergency');
    }

    public function professionals()
    {
        return view('services.professionals');
    }
}
