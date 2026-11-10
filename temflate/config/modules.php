<?php
declare(strict_types=1);

// One configuration for each of the four management modules.
const MODULES = [
    'doctor' => [
        'label' => 'Doctors', 'singular' => 'Doctor',
        'table' => 'doctor', 'pk' => 'docID',
        'fields' => [
            'docLName' => ['label' => 'Last Name', 'type' => 'text', 'max' => 50],
            'docFName' => ['label' => 'First Name', 'type' => 'text', 'max' => 50],
            'docAddress' => ['label' => 'Address', 'type' => 'text', 'max' => 50],
            'docSpecial' => ['label' => 'Special', 'type' => 'text', 'max' => 50],

        ],
        'search' => ['docID', 'docLName', 'docFName', 'docAddress', 'docSpecial'],
    ],
    'patient' => [
        'label' => 'Patients', 'singular' => 'Patient',
        'table' => 'patient', 'pk' => 'patID',
        'fields' => [
            'patFName' => ['label' => 'Patient Name', 'type' => 'text', 'max' => 50],
            'patLName' => ['label' => 'Patient Name', 'type' => 'text', 'max' => 50],
            'patBDate' => ['label' => 'Date', 'type' => 'date'],
            'patTelNo' => ['label' => 'Telephone', 'type' => 'text', 'max' => 15],

        ],
        'search' => ['patID', 'patFName', 'patLName', 'patBDate', 'patTelno'],
    ],
    'consultation' => [
        'label' => 'Consultations', 'singular' => 'Consultation',
        'table' => 'consultation', 'pk' => 'consulID',
        'fields' => [
            'patID' => [
                'label' => 'Patient', 'type' => 'select',
                'sql' => "SELECT patID AS id, CONCAT(patFName, ' ', patLName) AS label FROM patient ORDER BY patLName, patFName",
            ],
            'docID' => [
                'label' => 'Doctor', 'type' => 'select',
                'sql' => "SELECT docID AS id, CONCAT(docFName, ' ', docLName) AS label FROM doctor ORDER BY docLName, docFName",
            ],
            'consulDate' => ['label' => 'DateTime', 'type' => 'datetime'],
            'diagnosis' => ['label' => 'Diagnosis', 'type' => 'int',' min'=> 5, 'max'=> 30],
            'prescription' => ['label' => 'Prescription', 'type' => 'text', 'max'=> 30],


        ],
        'search' => ['consulID', 'patID', 'docID', 'consulDate','diagnosis','prescription'],
    ],
];

function get_module(string $key): array
{
    if (!isset(MODULES[$key])) {
        http_response_code(404);
        exit('Unknown module.');
    }
    return MODULES[$key];
}
