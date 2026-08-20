<?php

namespace App\Repository\Eloquent;

use App\Models\ContactMessage;
use App\Repository\Contracts\ContactMessageRepositoryInterface;

class ContactMessageRepository implements ContactMessageRepositoryInterface
{
    protected $model;

    public function __construct(ContactMessage $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->latest()->get();
    }

    public function getToday()
    {
        return $this->model->today()->latest()->get();
    }

    public function getBetweenDates($startDate, $endDate)
    {
        return $this->model->betweenDates($startDate, $endDate)->latest()->get();
    }

    public function getByEmail($email)
    {
        return $this->model->byEmail($email)->latest()->get();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete($id)
    {
        $message = $this->getById($id);
        return $message->delete();
    }

    public function getLatest($limit = 10)
    {
        return $this->model->latest()->limit($limit)->get();
    }
}
