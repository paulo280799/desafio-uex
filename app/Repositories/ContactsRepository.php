<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactsRepository
{
    public function __construct(
        protected Contact $contact
    ) {
    }

    public function getAll(array $filters): LengthAwarePaginator
    {
        $orderBy = $filters['order_by'] ?? 'name';
        $orderType = $filters['order_type'] ?? 'asc';

        $perPage = $filters['per_page'] ?? 15;

        return $this->contact
        ->when(isset($filters['search']), function ($q) use ($filters) {
            $search = $filters['search'];
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        })
        ->orderBy($orderBy, $orderType)
        ->paginate($perPage);
    }

    public function create(array $data): Contact
    {
        return $this->contact->create($data);
    }

    public function findById(int $id): ?Contact
    {
        return $this->contact->findOrFail($id);
    }

    public function update(int $id, array $data): Contact
    {
        $contact = $this->findById($id);
        $contact->update($data);

        return $contact;
    }

    public function delete(int $id): bool
    {
        $contact = $this->findById($id);

        return $contact->delete();
    }
}
