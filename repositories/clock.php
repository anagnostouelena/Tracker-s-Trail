<?php

// Function to perform checkpoint action
function performCheckpoint() {
    // Perform your checkpoint action here
    echo "Checkpoint performed.\n";
}

// Function to start the timer
function startTimer() {
    return microtime(true);
}

// Simulated success status (0 or 1)
$success = 1; // You can replace this with your actual success value

// Check if success is achieved
if ($success == 1) {
    // Start the timer
    $startTime = startTimer();
    
    // Simulating some time passing
    sleep(2); // For example, waiting for 2 seconds
    
    // Perform checkpoint action
    performCheckpoint();
    
    // Calculate the elapsed time
    $endTime = microtime(true);
    $elapsedTime = $endTime - $startTime;
    echo "Elapsed time: $elapsedTime seconds.\n";
} else {
    // If success is not achieved, do nothing
    echo "Success not achieved. No action taken.\n";
}
?>
