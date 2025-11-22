<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Basic in-memory "game data" using PHP sessions.
 * No database – everything resets when session expires.
 */

// All available cases
$CASES = [
    'case_1' => [
        'title'    => 'The Vanishing Briefcase',
        'level'    => 1,
        'location' => 'Downtown Station',
        'summary'  => 'A locked briefcase went missing from a crowded train platform.',
        'suspect'  => 'Commuter in a blue jacket'
    ],
    'case_2' => [
        'title'    => 'Echoes in the Alley',
        'level'    => 2,
        'location' => 'Old Market Alley',
        'summary'  => 'Witnesses heard an argument, but nobody saw who fled the scene.',
        'suspect'  => 'Local shop owner'
    ],
    'case_3' => [
        'title'    => 'Signals in the Storm',
        'level'    => 3,
        'location' => 'Riverside Warehouse',
        'summary'  => 'Security cameras glitched right before a shipment disappeared.',
        'suspect'  => 'Night shift guard'
    ],
    'case_4' => [
        'title'    => 'Shadows in the Gallery',
        'level'    => 4,
        'location' => 'City Art Museum',
        'summary'  => 'A single painting is gone, but the alarms never fired.',
        'suspect'  => 'Curator on duty'
    ],
];

// --- Initialise session state ---

$_SESSION['player_name'] = $_SESSION['player_name'] ?? '';

$_SESSION['max_level_unlocked'] = $_SESSION['max_level_unlocked'] ?? 1; // 1..7
$_SESSION['current_level']      = $_SESSION['current_level'] ?? 1;

$_SESSION['priority_case'] = $_SESSION['priority_case'] ?? 'case_1';
$_SESSION['active_case']   = $_SESSION['active_case']   ?? $_SESSION['priority_case'];

if (!isset($_SESSION['case_progress'])) {
    $_SESSION['case_progress'] = [];
    foreach ($CASES as $id => $case) {
        $_SESSION['case_progress'][$id] = 10; // start at 10%
    }
}

$_SESSION['interrogation_notes'] = $_SESSION['interrogation_notes'] ?? [];

// --- Helper functions ---

function get_active_case_id(): string
{
    return $_SESSION['active_case'] ?? $_SESSION['priority_case'] ?? 'case_1';
}

function get_active_case(): ?array
{
    global $CASES;
    $id = get_active_case_id();
    return $CASES[$id] ?? null;
}

function set_active_case(string $id): void
{
    global $CASES;
    if (isset($CASES[$id])) {
        $_SESSION['active_case']   = $id;
        $_SESSION['priority_case'] = $id;
    }
}

function get_case_progress(string $id): int
{
    return (int)($_SESSION['case_progress'][$id] ?? 0);
}

function set_case_progress(string $id, int $value): void
{
    $value = max(0, min(100, $value));
    $_SESSION['case_progress'][$id] = $value;
}

/**
 * Unlock the next level after finishing the current one.
 */
function unlock_next_level(int $completedLevel): void
{
    if (!isset($_SESSION['max_level_unlocked'])) {
        $_SESSION['max_level_unlocked'] = 1;
    }

    if ($_SESSION['max_level_unlocked'] < 7 && $_SESSION['max_level_unlocked'] === $completedLevel) {
        $_SESSION['max_level_unlocked'] = $completedLevel + 1;
    }
}

/**
 * Get the first case that belongs to a given level.
 * If none exists, fall back to case_1.
 */
function get_case_for_level(int $level): string
{
    global $CASES;
    foreach ($CASES as $id => $case) {
        if ((int)$case['level'] === $level) {
            return $id;
        }
    }
    return 'case_1';
}
