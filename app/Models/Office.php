<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    /**
     * Setting Eloquent updated ad and created at
     */
    public $timestamps = false;

    /**
     * Gets the office id.
     */
    public function getId(): int
    {
        return $this->attributes['id'];
    }

    /**
     * Gets the office name.
     */
    public function getName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Sets the office name.
     */
    public function setName(string $name): self
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    /**
     * Gets the office address.
     */
    public function getAddress(): string
    {
        return $this->attributes['address'];
    }

    /**
     * Sets the office address.
     */
    public function setAddress(string $address): self
    {
        $this->attributes['address'] = $address;

        return $this;
    }
}
