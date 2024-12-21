<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\controllers\Controller;
use simplerest\core\traits\TimeExecutionTrait;
use simplerest\libs\SendinBlue\Client\Model\Contact;

class ContactsController extends Controller
{
    public function index($search = null)
    {
        if ($search) {
            return DB::table('contacts')->whereLike('full_name', "%$search%")
            ->orWhereLike('company', "%$search%")
            ->get();
        }

        return DB::table('contacts')->get();
    }

    public function create($fullName = null)
    {
        $fullName = $fullName ?? $_GET['full_name'] ?? null;
        
        if (!$fullName) {
            die('Full name is required.');
        }

        $data = [
            'full_name' => $fullName,
            'company' => $_GET['company'] ?? null,
            'website' => $_GET['website'] ?? null,
            'job_title' => $_GET['job_title'] ?? null,
            'phone_number_1' => $_GET['phone1'] ?? $_GET['phone'] ?? null,
            'phone_number_2' => $_GET['phone2'] ?? null,
            'notes' => $_GET['notes'] ?? null
        ];

        return DB::table('contacts')
        ->create($data);
    }

    public function show($id)
    {
        if (!$id) {
            die('Contact ID is required.');
        }

        return DB::table('contacts')
                ->where('id', $id)
                ->first();
    }

    public function update($id)
    {
        if (!$id) {
            die('Contact ID is required.');
        }

        $data = [];
        foreach (['full_name', 'company', 'website', 'job_title', 'phone_number_1', 
                'phone_number_2', 'notes', 'favorite'] as $field) {
            if (isset($_GET[$field])) {
                $data[$field] = $_GET[$field];
            }
        }

        return DB::table('contacts')
                ->where('id', $id)
                ->update($data);
    }

    public function delete($id)
    {
        if (!$id) {
            die('Contact ID is required.');
        }
        
        return DB::table('contacts')
                ->where('id', $id)
                ->delete();
    }

    public function toggle_favorite($id)
    {
        if (!$id) {
            die('Contact ID is required.');
        }

        $contact = DB::table('contacts')                    
        ->where('id', $id)
        ->asObject()   // devuelve objeto
        ->first();
        
        $newStatus = !$contact->favorite;

        return DB::table('contacts')
                ->where('id', $id)
                ->update(['favorite' => $newStatus]);
    }
}