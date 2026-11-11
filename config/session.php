<?php
return ['driver' => 'file', 'lifetime' => 120, 'files' => storage_path('framework/sessions'), 'cookie' => 'pmt_session', 'path' => '/', 'http_only' => true, 'same_site' => 'lax', 'lottery' => [2, 100]];
