<ul class="nav nav-tabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upcoming">Upcoming</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#completed">Completed</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled">Cancelled</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="upcoming">
        <?php require_once("booking-cards.php"); ?>
    </div>
    <div class="tab-pane fade" id="completed">
        <?php require_once("booking-cards.php"); ?>
    </div>
    <div class="tab-pane fade" id="cancelled">
        <?php require_once("booking-cards.php"); ?>
    </div>
</div>