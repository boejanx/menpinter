<?php

// Auto-include all PHP helper files in this directory
foreach (glob(__DIR__ . '/*.php') as $filename) {
    require_once $filename;
}
