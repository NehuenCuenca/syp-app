<?php

namespace App\Observers;

use App\Models\Contact;

class ContactObserver
{
    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        //
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
        $contact->code = $this->generateCode($contact);
    }

    public function updating(Contact $contact)
    {
        if ($contact->isDirty('company_name')) {
            $contact->code = $this->generateCode($contact);
        }
    }

    private function generateCode(Contact $contact)
    {
        $prefix = $contact->contact_type === 'Cliente' ? 'CLI' : 'PROV';
        $abbr = strtoupper(substr(preg_replace('/\s+/', '', $contact->company_name), 0, 4));

        $lastId = Contact::where('company_name', 'like', $abbr . '%')->count() + 1;

        return $prefix . '-' . $abbr . '-' . str_pad($lastId, 3, '0', STR_PAD_LEFT);
    }
}

