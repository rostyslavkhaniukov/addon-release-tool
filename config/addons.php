<?php

return [
    'addons' => [
        'webhook-addon',
        'document-prefill-addon',
        'notification-addon',
        'jira-addon',
        'tags-addon',
        'slack-notifier-addon',
        'prefill-from-source-addons',
        'quickbooks-invoice-addon',
        'export-to-source-addons',
        'google-spreadsheets-duplex-addon',
        'mailchimp-send-campaign-addon',
        'google-spreadsheets-postfinish-addon',
        'slate-prefill-addon',
        'set-signature-types-addon',
        'set-packet-name-addon',
        'mailchimp-add-recipient-addon',
        //'hide-fields-values-addon',
        'roles-users-management-addon',
        'create-slate-reminder-addon',
        // 'watcher-from-source-addons',
        'create-slate-addon',
        'google-cloud-print-addon',
        'smartsheet-export-addon',
        'packet-delete-addon',
        'role-reminder-addon',
        'open-as-role-addon',
        'dropdown-options-prefill-addon',
        'audit-trail-addon',
        'google-calendar-addon',
        'sms-notifier-addon',
        'google-spreadsheets-watcher-addon',
        'custom-field-value-addon',
        'create-slate-another-flow-addon',
        'lock-slate-bot',
        'two-way-binding-addons',
        'calculate-addon',
    ],
    'labels' => [
        [
            'name' => 'approved',
            'color' => '008672',
            'description' => 'Approved by applications and platform teams',
        ],
        [
            'name' => 'waiting for platform review',
            'color' => '1d76db',
            'description' => 'Approved by applications team and waiting for platform team',
        ],
        [
            'name' => 'conflicts',
            'color' => 'b60205',
            'description' => 'Pull request has conflicts with base branch',
        ],
        [
            'name' => 'need 1 approve',
            'color' => '750bb7',
            'description' => 'This PR need one more approve',
        ],
        [
            'name' => 'need 2 approves',
            'color' => '750bb7',
            'description' => 'This PR need two approves',
        ],
    ],
];
