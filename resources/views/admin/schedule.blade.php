<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'schedule';
render_admin_layout_start($page, 'Staff Schedule');

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$selected_day = $_GET['day'] ?? 'Monday';

$staff = [
  ['name'=>'Pedro','role'=>'Master Barber'],
  ['name'=>'Maya','role'=>'Braiding Specialist'],
  ['name'=>'Rico','role'=>'Tattoo Artist'],
  ['name'=>'Lisa','role'=>'Piercing Specialist'],
  ['name'=>'Jay','role'=>'Barber'],
  ['name'=>'Marcus','role'=>'Barber'],
];

$schedule = [
  'Monday'    => ['Pedro'=>['9:00 AM','4:00 PM'],'Maya'=>['10:00 AM','5:00 PM'],'Rico'=>['12:00 PM','7:00 PM'],'Lisa'=>['9:00 AM','2:00 PM'],'Jay'=>['1:00 PM','6:00 PM'],'Marcus'=>['9:00 AM','3:00 PM']],
  'Tuesday'   => ['Pedro'=>['9:00 AM','4:00 PM'],'Maya'=>['10:00 AM','4:00 PM'],'Rico'=>['12:00 PM','7:00 PM'],'Lisa'=>['9:00 AM','1:00 PM'],'Jay'=>['2:00 PM','6:00 PM'],'Marcus'=>['9:00 AM','2:00 PM']],
  'Wednesday' => ['Pedro'=>['9:00 AM','4:00 PM'],'Maya'=>['10:00 AM','5:00 PM'],'Rico'=>['12:00 PM','7:00 PM'],'Lisa'=>[],'Jay'=>['9:00 AM','3:00 PM'],'Marcus'=>['1:00 PM','6:00 PM']],
  'Thursday'  => ['Pedro'=>['9:00 AM','4:00 PM'],'Maya'=>['10:00 AM','4:00 PM'],'Rico'=>['12:00 PM','7:00 PM'],'Lisa'=>['9:00 AM','2:00 PM'],'Jay'=>['2:00 PM','6:00 PM'],'Marcus'=>['9:00 AM','3:00 PM']],
  'Friday'    => ['Pedro'=>['9:00 AM','6:00 PM'],'Maya'=>['10:00 AM','6:00 PM'],'Rico'=>['12:00 PM','8:00 PM'],'Lisa'=>['9:00 AM','4:00 PM'],'Jay'=>['1:00 PM','7:00 PM'],'Marcus'=>['9:00 AM','5:00 PM']],
  'Saturday'  => ['Pedro'=>['9:00 AM','6:00 PM'],'Maya'=>['10:00 AM','5:00 PM'],'Rico'=>['12:00 PM','7:00 PM'],'Lisa'=>['9:00 AM','2:00 PM'],'Jay'=>['1:00 PM','6:00 PM'],'Marcus'=>['9:00 AM','4:00 PM']],
  'Sunday'    => ['Pedro'=>[],'Maya'=>[],'Rico'=>['2:00 PM','6:00 PM'],'Lisa'=>[],'Jay'=>[],'Marcus'=>[]],
];

$time_positions = ['9:00 AM'=>0,'10:00 AM'=>1,'11:00 AM'=>2,'12:00 PM'=>3,'1:00 PM'=>4,'2:00 PM'=>5,'3:00 PM'=>6,'4:00 PM'=>7,'5:00 PM'=>8,'6:00 PM'=>9,'7:00 PM'=>10,'8:00 PM'=>11];
$total_slots = 12;
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="page-title">Staff Schedule</h1>
    <p class="page-subtitle">Manage staff availability and shifts</p>
  </div>
</div>

<!-- Day Selector -->
<div class="d-flex gap-2 overflow-auto pb-2 mb-4">
  <?php foreach ($days as $day): ?>
  <a href="?day=<?= $day ?>" class="day-btn <?= $selected_day===$day?'active':'' ?>">
    <div style="font-weight:600"><?= $day ?></div>
    <div style="font-size:0.75rem;opacity:0.8"><?= date('M j', strtotime("next $day")) ?></div>
  </a>
  <?php endforeach; ?>
</div>

<!-- Schedule Table -->
<div class="card mb-4" style="overflow:hidden">
  <div style="overflow-x:auto">
    <table class="table mb-0" style="--bs-table-bg:transparent">
      <thead>
        <tr style="background:var(--muted)">
          <th class="px-4 py-3" style="color:var(--foreground);border-bottom:1px solid var(--border)">Staff Member</th>
          <th class="px-4 py-3" style="color:var(--foreground);border-bottom:1px solid var(--border)">Role</th>
          <th class="px-4 py-3" style="color:var(--foreground);border-bottom:1px solid var(--border)">Shift Hours</th>
          <th class="px-4 py-3" style="color:var(--foreground);border-bottom:1px solid var(--border)">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($staff as $member): 
          $shifts = $schedule[$selected_day][$member['name']] ?? [];
          $working = count($shifts) === 2;
        ?>
        <tr style="border-bottom:1px solid var(--border)">
          <td class="px-4 py-3" style="font-weight:600"><?= $member['name'] ?></td>
          <td class="px-4 py-3" style="color:var(--muted-foreground);font-size:0.875rem"><?= $member['role'] ?></td>
          <td class="px-4 py-3">
            <?php if ($working): ?>
              <span style="font-weight:500"><?= $shifts[0] ?> – <?= $shifts[1] ?></span>
            <?php else: ?>
              <span style="color:var(--muted-foreground)">Off</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <span class="badge rounded-pill <?= $working?'badge-available':'badge-offduty' ?>" style="font-size:0.75rem;padding:0.4rem 0.8rem">
              <?= $working ? 'Available' : 'Off Duty' ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Visual Schedule -->
<div class="card p-4">
  <h5 style="font-weight:700;margin-bottom:1.5rem">Visual Schedule — <?= $selected_day ?></h5>
  <?php foreach ($staff as $member):
    $shifts = $schedule[$selected_day][$member['name']] ?? [];
    $working = count($shifts) === 2;
    $start_pct = $working && isset($time_positions[$shifts[0]]) ? ($time_positions[$shifts[0]] / $total_slots) * 100 : 0;
    $end_pct   = $working && isset($time_positions[$shifts[1]]) ? ($time_positions[$shifts[1]] / $total_slots) * 100 : 0;
    $width_pct = $end_pct - $start_pct;
  ?>
  <div class="d-flex align-items-center gap-3 mb-3">
    <div style="width:80px;font-weight:600;font-size:0.9rem;flex-shrink:0"><?= $member['name'] ?></div>
    <div class="schedule-bar-wrap flex-grow-1">
      <?php if ($working): ?>
      <div class="schedule-bar-fill" style="left:<?= $start_pct ?>%;width:<?= $width_pct ?>%">
        <?= $shifts[0] ?> – <?= $shifts[1] ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="d-flex justify-content-between mt-2" style="padding-left:96px;font-size:0.75rem;color:var(--muted-foreground)">
    <span>9 AM</span><span>12 PM</span><span>3 PM</span><span>6 PM</span><span>9 PM</span>
  </div>
</div>

<?php render_admin_layout_end(); ?>

