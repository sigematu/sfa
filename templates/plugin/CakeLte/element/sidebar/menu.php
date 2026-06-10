<!-- Add icons to the links using the .nav-icon class
     with font-awesome or any other icon font library -->

<?php
$monthlyClientProposalStats = $monthlyClientProposalStats ?? [
    'monthLabel' => date('Y/m'),
    'total' => 0,
    'byAssignee' => [],
];
?>

<li class="nav-item menu-is-opening menu-open">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-circle"></i>
    <p>
      [要対応]
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview" style="display: block;">
    <li class="nav-item">
      <a href="/sfa/client-proposals?q=&sales_status=5&sales_reason=" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>[顧客提案] 提案中</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="/sfa/bp-procurements?q=&sales_status=1&sales_reason=" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>[BP調達] 調達中</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="/sfa/client-business-developments?status=1" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>[顧客案件開拓] 進行中</p>
      </a>
    </li>
  </ul>
</li>
