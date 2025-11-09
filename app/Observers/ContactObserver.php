<?php

namespace App\Observers;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class ContactObserver
{
    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        $contact->code = substr($contact->contact_type, 0, 1) . $contact->id;
        $contact->save();
    } 

    /**
     * Handle the Contact "updated" event.
     */
    public function updated(Contact $contact): void
    {   
        //
    }

    /**
     * Handle the Contact "deleted" event.
     */
    public function deleted(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "restored" event.
     */
    public function restored(Contact $contact): void
    {
        //
    }

    /**
     * Handle the Contact "force deleted" event.
     */
    public function forceDeleted(Contact $contact): void
    {
        //
    }

    public function creating(Contact $contact)
    {
        //
    }

    public function updating(Contact $contact)
    {
        if ($contact->isDirty('contact_type')) {
            $contact->code = substr($contact->contact_type, 0, 1) . $contact->id;
        }
    }

}

