<?php

namespace Payroll\Repositories;

use App\Organization;
use App\User;
use Artisan;
use Config;
use DB;
use Illuminate\Support\Str;

class OrganizationsRepository
{
    protected $tenantHash;
    protected $tenantName;
    protected $tenant;
    protected $details;
    protected $userDetails;
    protected $user;

    public static function register($details = [], $userDetails = [])
    {
        $repository = new self;
        $repository->setDetails($details, $userDetails)
            ->createHash()
            ->saveTenant()
            ->createDatabase()
            ->updateDatabase();

        return $repository;
    }

    private function setName($tenantName)
    {
        $this->tenantName = $tenantName;

        return $this;
    }

    private function createHash()
    {
        $hash = Str::random(6) . time();
        $prefix = str_replace('-', '', str_slug($this->tenantName));
        $start = rand(0, strlen($prefix) - 6);

        $this->tenantHash = 'cloudwage_' . substr($prefix, $start, $start + 5) . $hash;

        return $this;
    }

    private function saveTenant()
    {
        $this->details['database'] = $this->tenantHash;

        DB::transaction(function () {
            $this->tenant = Organization::create($this->details);
            $this->userDetails['organization_id'] = $this->tenant->id;
            $this->userDetails['database'] = $this->tenant->database;
            $this->userDetails['is_master'] = true;
            $this->userDetails['password'] = bcrypt($this->userDetails['password']);

            $this->user = User::create($this->userDetails);
        });

        return $this;
    }

    private function createDatabase()
    {
        DB::statement('CREATE DATABASE ' . $this->tenantHash);

        return $this;
    }

    public function getTenant()
    {
        return $this->tenant;
    }

    public function getDatabase()
    {
        return $this->tenantHash;
    }

    public function updateDatabase()
    {
        $this->createConfig();
        Artisan::call('migrate', ['--database' => $this->getDatabase(), '--seed' => true]);

        return $this;
    }

    private function createConfig()
    {
        $defaults = Config::get('database.connections.' . env('DB_CONNECTION', 'mysql'));
        $defaults['database'] = $this->getDatabase();
        Config::set('database.connections.' . $this->getDatabase(), $defaults);

        return $this;
    }

    private function setDetails($details, $userDetails)
    {
        $this->details = $details;
        $this->userDetails = $userDetails;
        $this->setName($details['name']);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public static function makeConfig($name)
    {
        $defaults = Config::get('database.connections.mysql');
        $defaults['database'] = $name;
        Config::set('database.connections.' . $name, $defaults);
        DB::setDefaultConnection($name);
    }
}
