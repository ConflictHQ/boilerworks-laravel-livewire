<?php

return [
    'forms' => (bool) env('FEATURE_FORMS', false),
    'workflows' => (bool) env('FEATURE_WORKFLOWS', false),
    'search' => (bool) env('FEATURE_SEARCH', false),
    'horizon' => (bool) env('FEATURE_HORIZON', false),
];
