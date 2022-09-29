<?php

Route::prefix('scheduler')->name('scheduler.')->group(function () {
    Route::get('full-backup', 'SchedulerBackupController@fullBackup')->name('fullbackup');
    Route::post('full-backup', 'SchedulerBackupController@storeFullBackupSetting')->name('fullbackup.post');
    Route::get('db-backup', 'SchedulerBackupController@dbBackup')->name('dbbackup');
    Route::post('db-backup', 'SchedulerBackupController@storeDbBackupSetting')->name('dbbackup.post');
});


Route::prefix('ldap')->name('ldap.')->group(function () {
    Route::get('manual-import', 'LdapUserImportLogsController@manual')->name('manual');
    Route::get('auto-import', 'LdapUserImportLogsController@auto')->name('auto');
    Route::post('manual-import', 'LdapUserImportLogsController@manualImport')->name('manual.post');
    Route::post('auto-import', 'LdapUserImportLogsController@autoImport')->name('auto.post');
});


