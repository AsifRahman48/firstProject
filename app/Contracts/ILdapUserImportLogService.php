<?php
namespace App\Contracts;


interface ILdapUserImportLogService
{
    public function indexManual();

    public function indexAuto();

    public function store(array $data);
}
