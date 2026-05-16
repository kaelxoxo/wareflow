<?php
define('STRIPE_PK',             getenv('STRIPE_PK')             ?: '');
define('STRIPE_SK',             getenv('STRIPE_SK')             ?: '');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: '');
define('STRIPE_PLAN_CENTS',     (int)(getenv('STRIPE_PLAN_CENTS') ?: 500));
