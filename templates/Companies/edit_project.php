<?php
extract($projects[0]);

$presentMonth = date("M-Y");
$awardDate = date("M-Y", strtotime($award));

// $projectMileDueDate
if (date("m-Y", strtotime($maxExtendDate)) >= date("m-Y", strtotime($projectMileDueDate))) {
    $maxMonth = date("M-Y", strtotime($maxExtendDate));
} else {
    $maxMonth = date("M-Y", strtotime($projectMileDueDate));
}

if (isset($_GET['month'])) {
    $presentMonth = $_GET['month'];
    $nextMonth = date("M-Y", strtotime('+1 month', strtotime($presentMonth)));
    $preMonth = date("M-Y", strtotime('-1 month', strtotime($presentMonth)));
} else {
    $nextMonth = date("M-Y", strtotime('+1 month', strtotime($presentMonth)));
    $preMonth = date("M-Y", strtotime('-1 month', strtotime($presentMonth)));
}


?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<section class="page page-dashboard">
    <!-- PAGE-TITLE -->
    <div class="page-title skin-light">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <div class="heading ft-secondary">
                        <span class="icon"><i class="fa fa-project-diagram"></i></span>Edit Project
                    </div>
                </div>
                <div class="col-6">
                    <div class="actions-ctrl text-md-right">
                        <?php if ($page == "myproject") : ?>
                        <?= $this->Html->link('<i class="fa fa-list"></i><span>Back</span>', '/my-project/', ['class' => 'v-btn v-btn-secondary', 'escape' => false]); ?>
                        <?php
                        elseif ($page == "active") : ?>
                        <?= $this->Html->link('<i class="fa fa-list"></i><span>Back</span>', '/active-project/' . $mId, ['class' => 'v-btn v-btn-secondary', 'escape' => false]); ?>
                        <?php
                        elseif ($page == "timesheet") :
                        ?>
                        <?= $this->Html->link('<i class="fa fa-list"></i><span>Back</span>', '/timesheet/', ['class' => 'v-btn v-btn-secondary', 'escape' => false]); ?>
                        <?php
                        else :
                        ?>
                        <?= $this->Html->link('<i class="fa fa-list"></i><span>Back</span>', '/list-project/' . $mId, ['class' => 'v-btn v-btn-secondary', 'escape' => false]); ?>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PAGE-CONTENT -->
    <div class="page-content">
        <div class="container">
            <?= $this->Flash->render() ?>
            <!-- PROJECT ADD -->
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="url_id" value="<?= WEBURL; ?>">
                    <?= $this->Form->create(null, array('id' => 'project')) ?>
                    <div class="block">
                        <div class="header">
                            <h4 class="title">Edit Project Details</h4>
                        </div>
                        <div class="content ">
                            <div class="row" id="pro_data">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" name="project_id" value="<?= $id; ?>">
                                        <?php if ($miles) : foreach ($miles as $m) : ?>
                                        <input type="hidden" name="milesid[]" value="<?= $m['id']; ?>">
                                        <?php endforeach;
                                        endif; ?>
                                        <?php if ($miles) : foreach ($payments as $p) : ?>
                                        <input type="hidden" name="ptid[]" value="<?= $p['id']; ?>">
                                        <?php endforeach;
                                        endif; ?>
                                        <label for="">Project Name</label>
                                        <div class="adon-group pname">
                                            <input type="text" class="form-control" name="project_name"
                                                value="<?= $project_name; ?>" placeholder="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Client Name</label>
                                        <div class="adon-group cname">
                                            <input id="tags" class="form-control client" name="client_name"
                                                value="<?= $client; ?>">
                                            <a href="#" data-target="#add_client" data-toggle="modal"
                                                class="v-btn  v-btn-primary"><i class="fa fa-plus"></i><span>Add
                                                    Client</span></a>
                                        </div>
                                        <label id="tags-error-empty" class="error" for="tags"></label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Upwork ID</label>
                                        <div class="adon-group cname">
                                            <input type="number" id="upworkId" class="form-control" name="upwork_ref_id"
                                                value="<?= $upwork_ref_id ?>">
                                            <a href="javascript:void(0)" onclick="upworkDetail()"
                                                class="v-btn  v-btn-primary" style="width:40%">Contract</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Awarded On</label>
                                        <div class="adon-group award">
                                            <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                            <input class="datepicker form-control" type="text" placeholder=""
                                                name="awarded_on" value="<?= $award; ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Due Date</label>
                                        <div class="adon-group ddate">
                                            <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                            <input class="datepicker form-control" type="text" placeholder=""
                                                name="due_date" value="<?= $due_date; ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <!-- <?php
                                if (strtotime($extend_date) > strtotime($due_date)) {
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Extend Date</label>
                                        <div class="adon-group ddate">
                                            <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                            <input class="form-control" type="text" placeholder="" name="extend_date"
                                                value="<?= $extend_date; ?>" autocomplete="off" readonly>
                                        </div>
                                    </div>
                                </div>

                                <?php } ?> -->

                                <?php if (!empty($extend_date) && strtotime($extend_date) > strtotime($due_date)) { ?>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Extend Date</label>
                                        <div class="adon-group ddate">
                                            <span class="icon ft-primary">
                                                <i class="fa fa-calendar-alt"></i>
                                            </span>

                                            <input class="form-control"
                                                type="text"
                                                placeholder=""
                                                name="extend_date"
                                                value="<?= $extend_date; ?>"
                                                autocomplete="off"
                                                readonly>
                                        </div>
                                    </div>
                                </div>

                                <?php } ?>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Project Type</label>
                                        <div class="adon-group amt">
                                            <span class="icon ft-primary"><i class="fa fa-dollar-sign"></i></span>
                                            <select name="project_type" id="" class="form-control">
                                                <option value="Fixed Price"
                                                    <?php if ($type == 'Fixed Price') echo 'selected'; ?>>Fixed</option>
                                                <option value="Hourly" <?php if ($type == 'hourly') echo 'selected'; ?>>
                                                    Hourly</option>
                                            </select>
                                            <input type="text" class="form-control text-right" style="width:70px"
                                                placeholder="$3000" value="<?= $amount; ?>" name="amount"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Project Manager</label>
                                        <div class="adon-group pmgr">
                                            <select name="project_manager_id" class="form-control" id=""
                                                style="color:black;">
                                                <option value="">Select Project Manger</option>
                                                <?php foreach ($manager as $m) : ?>

                                                <option value="<?= $m->id; ?>"
                                                    <?php if ($project_manager_id == $m->id) echo 'selected'; ?>>
                                                    <?= $m->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Tech Lead</label>
                                        <div class="adon-group tl">
                                            <select name="tech_lead_id" class="form-control" id="" style="color:black;">
                                                <option value="">Select Tech Lead</option>
                                                <?php foreach ($techlead as $m) : ?>

                                                <option value="<?= $m->id; ?>"
                                                    <?php if ($tech_lead_id == $m->id) echo 'selected'; ?>>
                                                    <?= $m->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">BDE</label>
                                        <div class="adon-group bd">
                                            <select name="bd_id" class="form-control" id="" style="color:black;">
                                                <option value="">Select BDE</option>
                                                <?php foreach ($bdteam as $m) : ?>

                                                <option value="<?= $m->id; ?>"
                                                    <?php if ($bd_id == $m->id) echo 'selected'; ?>><?= $m->name; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Resources</label>
                                        <div class="adon-group res">
                                            <select name="resources[]" class="form-control" multiple id="langOpt">
                                                <?php $res = explode(',', $resources);
                                                foreach ($resource as $m) : ?>

                                                <option value="<?= $m->id; ?>"
                                                    <?php if (in_array($m->id, $res)) echo 'selected'; ?>>
                                                    <?= $m->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Source</label>
                                        <div class="adon-group res">
                                            <select name="source" class="form-control">
                                                <option value="Regular" <?php if (!empty($projects)) {
                                                                            echo ($source == 'Regular') ? 'selected' : "";
                                                                        } ?>>Regular</option>
                                                <option value="External" <?php if (!empty($projects)) {
                                                                                echo ($source == 'External') ? 'selected' : "";
                                                                            } ?>>External</option>
                                                <option value="Expertal" <?php if (!empty($projects)) {
                                                                                echo ($source == 'Expertal') ? 'selected' : "";
                                                                            } ?>>Expertal</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Hourly Rate</label>
                                        <div class="adon-group award">
                                            <!-- <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span> -->
                                            <input class="form-control" type="text" name="hourly_rate"
                                                value="<?= (!empty($projects)) ? $hourly_rate : null ?>"
                                                autocomplete="off" value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mt-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="bill"
                                                <?= (!empty($projects)) ? ($bill == "Non Billable" ? "checked" : null) : null ?>>
                                            <label class="form-check-label mx-md-2" for="">Check for Non
                                                Billable</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mt-md-4">
                                        <div class="form-check">
                                            <h4 class="font-weight-bold" style="font-size:15px">Total Budgeted Hours :
                                            <?= !empty($projects) ? ($total_pm_amount ? ($hourly_rate != 0 ? round($total_pm_amount / $hourly_rate) : 0) : 0) : 0 ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-md-4">
                                        <div class="form-check">
                                            <h4 class="font-weight-bold" style="font-size:15px">Total Actual Hours :
                                                <?= $total_actual_hours ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-md-4">
                                        <div class="form-check">
                                            <h4 class="font-weight-bold" style="font-size:15px">Total Allocated Hours :
                                            <?= $total_allocated_hours ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row-reverse">
                                <div class="form-group float-right" style="margin-top:22px;">
                                    <button type="submit" name="submit" class="v-btn v-btn-secondary float-right"
                                        id="save_project"><span>Update Project</span></button>
                                </div>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>
                    <div class="block">
                        <div class="header">
                            <h4 class="title">Project Milestone <a href="#" data-target="#add_milestone"
                                    data-toggle="modal" class="v-btn v-btn-primary float-right">
                                    <i class="fa fa-plus"></i> <span>Add Milestone</span></a>
                            </h4>
                            <hr>
                            <div class="col-md-3 float-right">
                                <div class="adon-group form-group">
                                    <select name="" id="mile" class="form-control" onchange="allMilestone(<?= $id ?>)">
                                        <option value="not">Not Completed</option>
                                        <option value="all">View All</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="content table-responsive">
                            <table class="table table-default" id="table_data">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width:500px">Title</th>
                                        <th>Due_Date</th>
                                        <th>Amount</th>
                                        <th>status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <?php if ($miles) : ?>
                                <?php
                                    foreach ($miles as $m) :
                                        if ($m['status'] != 'Completed') :
                                    ?>
                                <tbody id="rowm<?= $m['id']; ?>" class="">

                                    <tr class="active">
                                        <td>
                                            <label class="labels" id="lm<?= $m['id']; ?>"
                                                onclick="mlabel(<?= $m['id']; ?>)"><i
                                                    class="fa fa-chevron-up"></i></label>
                                            <input type="checkbox" name="milestoneOne" id="m<?= $m['id']; ?>"
                                                data-toggle="toggle">
                                        </td>
                                        <td><?php
                                            $displayTitle = $m['title'];

                                            if (!empty($m['milestone_month_year'])) {
                                                $displayTitle .= ' ' . $m['milestone_month_year'];
                                            } echo $displayTitle; ?>
                                        </td>
                                        <td><?= $m['due_date']; ?></td>
                                        <td>$<?= $m['amount']; ?></td>
                                        <td>
                                            <select name="mstatus" class="form-control status" id="<?= $m['id']; ?>"
                                                data-type="miles" data-url="<?= WEBURL; ?>">
                                                <option value="Yet to start"
                                                    <?php if ($m['status'] == 'Yet to start') echo 'selected'; ?>>Yet to
                                                    start</option>
                                                <option value="Inprogress"
                                                    <?php if ($m['status'] == 'Inprogress') echo 'selected'; ?>>In
                                                    progress</option>
                                                <option value="Completed"
                                                    <?php if ($m['status'] == 'Completed') echo 'selected'; ?>>completed
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="#" class="icon mtask" data-toggle="modal" data-target="#add_task"
                                                onclick="taskValue(<?= $m['id']; ?>)" title="Add Task"><i
                                                    class="fa fa-plus"></i></a>
                                            <a href="#" class="icon" data-toggle="modal" data-target="#edit_milestone"
                                                onclick="passValue('edit',<?= $m['id']; ?>)"> <i
                                                    class="fa fa-pencil-alt"></i> </a>
                                            <a href="javascript::void(0);" class="icon" onclick="passValue('delete',<?= $m['id']; ?>)"> <i
                                                    class="fa fa-trash-alt"></i> </a>
                                            <a href="javascript::void(0);" class="icon" onclick="passValue('copy',<?= $m['id']; ?>)"> 
                                                <i class="fa fa-clone" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody class="rowtm<?= $m['id']; ?>" id="rowtm<?= $m['id']; ?>">
                                    <?php if (count($m['task_list']) > 0) : ?>
                                    <?php foreach ($m['task_list'] as $mt) : ?>

                                    <tr id="rowt<?= $mt['id']; ?>">
                                        <td></td>
                                        <td><?= $mt['task']; ?></td>
                                        <td><?= $mt['due_date']; ?></td>
                                        <td>-</td>
                                        <td>
                                            <select name="mtstatus" class="form-control status" id="<?= $mt['id']; ?>"
                                                data-type="tasks" data-url="<?= WEBURL; ?>">
                                                <option value="Yet to start"
                                                    <?php if ($mt['status'] == 'Yet to start') echo 'selected'; ?>>Yet
                                                    to start</option>
                                                <option value="Inprogress"
                                                    <?php if ($mt['status'] == 'Inprogress') echo 'selected'; ?>>In
                                                    progress</option>
                                                <option value="Completed"
                                                    <?php if ($mt['status'] == 'Completed') echo 'selected'; ?>>
                                                    completed</option>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="#" class="icon" data-toggle="modal" data-target="#edit_task"
                                                onclick="passtaskValue('edit',<?= $mt['id']; ?>)"> <i
                                                    class="fa fa-pencil-alt"></i> </a><a href="#"
                                                class="icon delete-milestone" data-id="'+response.id+'"
                                                onclick="passtaskValue('delete',<?= $mt['id']; ?>)"> <i
                                                    class="fa fa-trash-alt"></i> </a>
                                        </td>
                                    </tr>

                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <?php endif;
                                    endforeach; ?>
                                <?php endif; ?>

                            </table>
                        </div>
                    </div>
                    <div class="block">
                        <div class="header">
                            <div class="row">
                                <div class="col-md-3">
                                    <h4 class="title">Resources Allocation </h4>
                                    <input type="hidden" id="url" value="<?= WEBURL; ?>">
                                </div>
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-3">
                                </div>
                                <!-- <div class="col-md-3">
                                    <div class="adon-group form-group">
                                        <select class="form-control" onchange="location=this.value">
                                            <option
                                                value="<?= $this->Url->build("/edit-project/$id?res=not completed&month=$presentMonth") ?>"
                                                <?php if (isset($_GET['res']) && $_GET['res'] == "not completed") echo "selected" ?>>
                                                Not Completed
                                            </option>
                                            <option
                                                value="<?= $this->Url->build("/edit-project/$id?res=all&month=$presentMonth") ?>"
                                                <?php if (isset($_GET['res']) && $_GET['res'] == "all") echo "selected" ?>>
                                                View All
                                            </option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-3">
                                    <?php
                                    if (strtotime($awardDate) < strtotime($presentMonth)) :
                                    ?>
                                    <a href="<?= $this->Url->build("/edit-project/$id?month=$preMonth") ?>">
                                        <i class="fa fa-arrow-left" aria-hidden="true" style="cursor:pointer">
                                        </i>
                                    </a>
                                    <?php
                                    endif;
                                    ?>
                                    <span style="text-align: right;" id="nextPreMonth">
                                        <?= $presentMonth ?>
                                    </span>
                                    <?php
                                    if (strtotime($presentMonth) < strtotime($maxMonth)) :  ?>
                                    <a href="<?= $this->Url->build("/edit-project/$id?month=$nextMonth") ?>">
                                        <i class="fa fa-arrow-right" aria-hidden="true" style="cursor:pointer">
                                        </i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="content table-responsive">
                            <table class="table table-default table-sm allocation-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <?php if (!empty($projects)) :
                                            foreach ($reslist as $r) : ?>
                                        <th><?= $r['name']; ?></th>
                                        <?php endforeach;
                                        endif; ?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <!-- <tbody id="allRes">
                                    <?php if (!empty($projects)) :
                                        $i = 1;
                                        // echo '<pre>';
                                        // print_r($resourceList);
                                        // die;
                                        foreach ($resourceList as $rl) :
                                            if (isset($_GET['res']) && $_GET['res'] == "not completed") :
                                                if ($rl['status'] != "Completed") :
                                                    if (date("M-Y", strtotime($rl['due_date'])) == $presentMonth) :
                                    ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td style="text-align: left;"><?= $rl['title']; ?></td>
                                        <?php if (count($rl['res']) > 0) : $hrs = 0;
                                                                $wrk = 0;
                                                                foreach ($rl['res'] as $r) : ?>
                                        <td>
                                            <input type="text"
                                                class="form-control aloc-input changeTime hrs_<?= $rl['id']; ?>"
                                                data-id="<?= $rl['id']; ?>" value="<?= $r['time']; ?>"
                                                data-user="<?= $r['id']; ?>" placeholder="hrs">

                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                placeholder="hrs" value="<?= $r['worked']; ?>">
                                        </td>
                                        <?php $hrs += $r['time'];
                                                                    $wrk += $r['worked'];
                                                                endforeach;
                                                            endif; ?>
                                        <td>
                                            <input type="text" value="<?= $hrs; ?>"
                                                class="form-control aloc-input disabled totalmgr_<?= $rl['id']; ?>"
                                                disabled placeholder="hrs">

                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                placeholder="hrs" value="<?= $wrk; ?>">
                                        </td>
                                    </tr>
                                    <?php $i++;
                                                    endif;
                                                endif;
                                            elseif (isset($_GET['res']) && $_GET['res'] == "all") :
                                                if (date("M-Y", strtotime($rl['due_date'])) == $presentMonth) :
                                                    ?>

                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td style="text-align: left;"><?= $rl['title']; ?></td>
                                        <?php if (count($rl['res']) > 0) : $hrs = 0;
                                                            $wrk = 0;
                                                            foreach ($rl['res'] as $r) : ?>
                                        <td>
                                            <input type="text"
                                                class="form-control aloc-input changeTime hrs_<?= $rl['id']; ?>"
                                                data-id="<?= $rl['id']; ?>" value="<?= $r['time']; ?>"
                                                data-user="<?= $r['id']; ?>" placeholder="hrs">

                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                placeholder="hrs" value="<?= $r['worked']; ?>">
                                        </td>
                                        <?php $hrs += $r['time'];
                                                                $wrk += $r['worked'];
                                                            endforeach;
                                                        endif; ?>
                                        <td>
                                            <input type="text" value="<?= $hrs; ?>"
                                                class="form-control aloc-input disabled totalmgr_<?= $rl['id']; ?>"
                                                disabled placeholder="hrs">

                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                placeholder="hrs" value="<?= $wrk; ?>">
                                        </td>
                                    </tr>

                                    <?php
                                                endif;
                                            else :
                                                // if ($rl['status'] != "Completed") :
                                                if (date("M-Y", strtotime($rl['due_date'])) == $presentMonth) :
                                                ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td style="text-align: left;"><?= $rl['title']; ?></td>
                                        <?php if (count($rl['res']) > 0) : $hrs = 0;
                                                            $wrk = 0;
                                                            foreach ($rl['res'] as $r) : ?>
                                        <td>
                                            <input type="text"
                                                class="form-control aloc-input changeTime hrs_<?= $rl['id']; ?>"
                                                data-id="<?= $rl['id']; ?>" value="<?= $r['time']; ?>"
                                                data-user="<?= $r['id']; ?>" placeholder="hrs">

                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                placeholder="hrs" value="<?= $r['worked']; ?>">
                                        </td>
                                        <?php $hrs += $r['time'];
                                                                $wrk += $r['worked'];
                                                            endforeach;
                                                        endif; ?>
                                        <td>
                                            <input type="text" value="<?= $hrs; ?>"
                                                class="form-control aloc-input disabled totalmgr_<?= $rl['id']; ?>"
                                                disabled placeholder="hrs">

                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                placeholder="hrs" value="<?= $wrk; ?>">
                                        </td>
                                    </tr>

                                    <?php
                                                // endif;
                                                endif;
                                            endif;
                                        endforeach;
                                    endif; ?>
                                </tbody> -->
                                <tbody id="allRes">
                                    <?php 
                                    if (!empty($projects)) :
                                        $i = 1;

                                        foreach ($resourceList as $rl) :

                                            // --- FILTER 1: Not Completed ---
                                            if (isset($_GET['res']) && $_GET['res'] == "not completed") :
                                                if ($rl['status'] != "Completed" && date("M-Y", strtotime($rl['due_date'])) == $presentMonth) :
                                    ?>
                                                    <tr>
                                                        <td><?= $i; ?></td>
                                                        <td style="text-align:left;"><?= $rl['title']; ?></td>

                                                        <?php 
                                                        $hrs = 0; 
                                                        $wrk = 0; 
                                                        if (count($rl['res']) > 0) :
                                                            foreach ($rl['res'] as $r) : 
                                                        ?>
                                                            <td>
                                                                <input type="text" class="form-control aloc-input changeTime hrs_<?= $rl['id']; ?>"
                                                                    data-id="<?= $rl['id']; ?>" 
                                                                    value="<?= $r['time']; ?>" 
                                                                    data-user="<?= $r['id']; ?>" placeholder="hrs">

                                                                <input type="text" class="form-control aloc-input disabled" disabled
                                                                    value="<?= $r['worked']; ?>" placeholder="hrs">
                                                            </td>
                                                        <?php 
                                                                $hrs += $r['time'];
                                                                $wrk += $r['worked'];
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                        <td>
                                                            <input type="text" class="form-control aloc-input disabled totalmgr_<?= $rl['id']; ?>" 
                                                                disabled value="<?= $hrs; ?>" placeholder="hrs">

                                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                                value="<?= $wrk; ?>" placeholder="hrs">
                                                        </td>
                                                    </tr>
                                    <?php 
                                                    $i++; 
                                                endif;

                                            // --- FILTER 2: All ---
                                            elseif (isset($_GET['res']) && $_GET['res'] == "all") :
                                                if (date("M-Y", strtotime($rl['due_date'])) == $presentMonth) :
                                    ?>
                                                    <tr>
                                                        <td><?= $i; ?></td>
                                                        <td style="text-align:left;"><?= $rl['title']; ?></td>

                                                        <?php 
                                                        $hrs = 0; 
                                                        $wrk = 0; 
                                                        if (count($rl['res']) > 0) :
                                                            foreach ($rl['res'] as $r) : 
                                                        ?>
                                                            <td>
                                                                <input type="text" class="form-control aloc-input changeTime hrs_<?= $rl['id']; ?>"
                                                                    data-id="<?= $rl['id']; ?>" 
                                                                    value="<?= $r['time']; ?>" 
                                                                    data-user="<?= $r['id']; ?>" placeholder="hrs">

                                                                <input type="text" class="form-control aloc-input disabled" disabled
                                                                    value="<?= $r['worked']; ?>" placeholder="hrs">
                                                            </td>
                                                        <?php 
                                                                $hrs += $r['time'];
                                                                $wrk += $r['worked'];
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                        <td>
                                                            <input type="text" class="form-control aloc-input disabled totalmgr_<?= $rl['id']; ?>" 
                                                                disabled value="<?= $hrs; ?>" placeholder="hrs">

                                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                                value="<?= $wrk; ?>" placeholder="hrs">
                                                        </td>
                                                    </tr>
                                    <?php 
                                                    $i++; 
                                                endif;

                                            // --- FILTER 3: Default ---
                                            else :
                                                if (date("M-Y", strtotime($rl['due_date'])) == $presentMonth) :
                                    ?>
                                                    <tr>
                                                        <td><?= $i; ?></td>
                                                        <td style="text-align:left;"><?= $rl['title']; ?></td>

                                                        <?php 
                                                        $hrs = 0; 
                                                        $wrk = 0; 
                                                        if (count($rl['res']) > 0) :
                                                            foreach ($rl['res'] as $r) : 
                                                        ?>
                                                            <td>
                                                                <input type="text" class="form-control aloc-input changeTime hrs_<?= $rl['id']; ?>"
                                                                    data-id="<?= $rl['id']; ?>" 
                                                                    value="<?= $r['time']; ?>" 
                                                                    data-user="<?= $r['id']; ?>" placeholder="hrs">

                                                                <input type="text" class="form-control aloc-input disabled" disabled
                                                                    value="<?= $r['worked']; ?>" placeholder="hrs">
                                                            </td>
                                                        <?php 
                                                                $hrs += $r['time'];
                                                                $wrk += $r['worked'];
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                        <td>
                                                            <input type="text" class="form-control aloc-input disabled totalmgr_<?= $rl['id']; ?>" 
                                                                disabled value="<?= $hrs; ?>" placeholder="hrs">

                                                            <input type="text" class="form-control aloc-input disabled" disabled
                                                                value="<?= $wrk; ?>" placeholder="hrs">
                                                        </td>
                                                    </tr>
                                    <?php 
                                                    $i++; 
                                                endif;
                                            endif;

                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="block">
                        <div class="header">
                            <h4 class="title">Payment History <a href="#" data-target="#add_payment_received"
                                    data-toggle="modal" class="v-btn v-btn-primary float-right"><i
                                        class="fa fa-plus"></i><span>Add Payment</span></a></h4>
                            </h4>
                        </div>
                        <div class="content table-responsive">
                            <table class="table table-default nowarp">
                                <thead>
                                    <tr>

                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Received_Amt</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="payment_data">
                                    <?php if ($payments) : ?>
                                    <?php foreach ($payments as $p) : ?>
                                    <tr id="rowp<?= $p['id']; ?>">

                                        <td><?= $p['description']; ?></td>
                                        <td><?= $p['payment_date']; ?></td>
                                        <td>
                                            $<?= $p['receive_amt']; ?>
                                        </td>
                                        <td>
                                            <select name="pstatus" class="form-control status" id="<?= $p['id']; ?>"
                                                data-type="payment" data-url="<?= WEBURL; ?>">
                                                <option value="Billed"
                                                    <?php if ($p['status'] == 'Billed') echo 'selected'; ?>>Billed
                                                </option>
                                                <option value="Paid"
                                                    <?php if ($p['status'] == 'Paid') echo 'selected'; ?>>
                                                    Paid</option>
                                                <option value="Estimated"
                                                    <?php if ($p['status'] == 'Estimated') echo 'selected'; ?>>Estimated
                                                </option>

                                            </select>
                                        </td>
                                        <td>
                                            <a href="#" class="icon" data-toggle="modal" data-target="#edit_payment"
                                                onclick="passPayment('edit',<?= $p['id']; ?>)"> <i
                                                    class="fa fa-pencil-alt"></i> </a>
                                            <a href="#" class="icon" onclick="passPayment('delete',<?= $p['id']; ?>);">
                                                <i class="fa fa-trash-alt"></i> </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>

<!-- ADD COMPANY MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="add_client">
    <?= $this->Form->create(null, array('id' => 'clients')) ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Client Name</label>
                            <div class="adon-group clname">
                                <span class="icon ft-primary"><i class="fa fa-user"></i></span>
                                <input type="text" class="form-control" name="client_name" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Email Id</label>
                            <div class="adon-group emailclass">
                                <span class="icon ft-primary"><i class="fa fa-envelope"></i></span>
                                <input type="text" name="email" class="form-control" placeholder="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">Phone Number</label>
                            <div class="adon-group">
                                <input type="text" name="country_code" class="form-control text-center" placeholder=""
                                    value="+91" autocomplete="off" style="border-right: 1px solid #eee; width:45px;">
                                <input type="text" name="contact_no" class="form-control" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Location</label>
                            <div class="adon-group">
                                <span class="icon ft-primary"><i class="fa fa-map-marker-alt"></i></span>
                                <input type="text" name="location" class="form-control" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" class="v-btn v-btn-primary" id="saveclient">Save Client</a>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<!-- ADD MILESTONE MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="add_milestone">
    <?= $this->Form->create(null, array('id' => 'milestone')) ?>


    <input type="hidden" name="project_id" value="<?= $id; ?>">


    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Milestone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Milestone Title</label>
                            <div class="adon-group mt">
                                <input type="hidden" name="project_id" value="<?= $id; ?>">
                                <input type="text" class="form-control" name="title" placeholder="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Due Date</label>
                            <div class="adon-group mddate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker" name="due_date" id="dueDate"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">Amount</label>
                            <div class="adon-group mamt">
                                <span class="icon ft-primary"><i class="fa fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="amount" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="savemile">Add Milestone</button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>


<!-- Edit MILESTONE MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit_milestone">
    <?= $this->Form->create(null, array('id' => 'editmilestone')) ?>


    <input type="hidden" name="project_id" value="<?= $id; ?>">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Milestone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Milestone Title</label>
                            <div class="adon-group mt">
                                <input type="hidden" class="form-control" name="mile_id" id="mile_id" placeholder="">
                                <input type="text" class="form-control" name="title" id="title" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Due Date</label>
                            <div class="adon-group mddate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker" name="due_date" id="due_date"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">Amount</label>
                            <div class="adon-group mamt">
                                <span class="icon ft-primary"><i class="fa fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="amount" id="amount" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="editmile">Update Milestone</button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>


<!-- ADD PAYMENT RECEIVED MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="add_payment_received">
    <?= $this->Form->create(null, array('id' => 'payment')) ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Description</label>
                            <div class="adon-group ades">
                                <input type="hidden" name="project_id" value="<?= $id; ?>">
                                <input type="text" class="form-control" name="description" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Date</label>
                            <div class="adon-group pdate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control" id="datepicker" name="payment_date" placeholder=""
                                    autocomplete="off" style="background:#fff;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">Received Payment</label>
                            <div class="adon-group ramt">
                                <span class="icon"><i class="fa fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="receive_amt" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="savepayment">Add To Payment
                    History</button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>


<!-- EDIT PAYMENT RECEIVED MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit_payment">
    <?= $this->Form->create(null, array('id' => 'editpayment')) ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payment History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Description</label>
                            <div class="adon-group des">
                                <input type="hidden" class="form-control" name="payment_id" id="payment_id"
                                    placeholder="">
                                <input type="text" class="form-control" name="description" id="description"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Date</label>
                            <div class="adon-group pdate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker" name="payment_date" placeholder=""
                                    id="payment_date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">Received Payment</label>
                            <div class="adon-group ramt">
                                <span class="icon"><i class="fa fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="receive_amt" id="receive_amt"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="edtpayment">Update Payment
                    History</button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>


<!-- ADD TASK MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="add_task">
    <?= $this->Form->create(null, array('id' => 'task_form')) ?>


    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Task</label>
                            <div class="adon-group mtk">
                                <input type="hidden" name="milestone_id" value="">
                                <input type="text" class="form-control" name="task" placeholder="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Due Date</label>
                            <div class="adon-group tdate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker" name="due_date" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                    <label for="">Status</label>
                    <div class="adon-group">
                        <select name="status" class="form-control" id="">
                            <option value="Yet to start">Yet to start</option>
                            <option value="Inprogress">In progress</option>
                            <option value="Completed">completed</option>
                        </select>
                    </div>
                </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="savetask">Add Task</button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<!-- EDIT TASK MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit_task">
    <?= $this->Form->create(null, array('id' => 'edittask_form')) ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Task</label>
                            <div class="adon-group mt">
                                <input type="hidden" name="task_id" id="task_id">
                                <input type="hidden" name="milestone_id" id="milestone_id">
                                <input type="text" class="form-control" name="task" id="task" placeholder=""
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Due Date</label>
                            <div class="adon-group mddate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker" id="task_due_date" name="due_date"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                    <label for="">Status</label>
                    <div class="adon-group">
                        <select name="status" class="form-control" id="">
                            <option value="Yet to start">Yet to start</option>
                            <option value="Inprogress">In progress</option>
                            <option value="Completed">completed</option>
                        </select>
                    </div>
                </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="v-btn v-btn-base cancel" data-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="edittask">Update Task</button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
<script>
$("input[type=text]").on("focus", function() {
    if ($(this).val() == 0)
        $(this).val('');
});
//add form
var mvalidator = $("#milestone").validate({
    onfocusout: false,
    onkeyup: false,

    rules: {
        title: {
            required: true,
        },
        due_date: {
            required: true,
        },
        amount: {
            required: true
        },
    },
    messages: {
        title: {
            required: "Please enter Title",

        },
        due_date: {
            required: "Please enter Date",
        },
        amount: {
            required: "Please enter amount",
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "title")
            error.insertAfter(".mt");
        else if (element.attr("name") == "due_date")
            error.insertAfter(".mddate");
        else if (element.attr("name") == "amount")
            error.insertAfter(".mamt");
    },
    submitHandler: function(form) {
        $('#savemile').html('sending..');
        var url = $('#url_id').val();

        let lastDueDate = new Date("<?= $due_date; ?>");
        let extendedDate = new Date($("#dueDate").val());
        // console.log(extendedDate.getTime() + " ----  " + lastDueDate.getTime());

        if (extendedDate.getTime() > lastDueDate.getTime()) {
            if (confirm(`Do you want to extend the project due date to milestone end date?`)) {
                $.ajax({
                    url: "<?= $this->Url->build('/companies/addMilestone') ?>",
                    type: "POST",
                    data: $('#milestone').serialize(),
                    dataType: "json",
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        } else {
            $.ajax({
                url: "<?= $this->Url->build('/companies/addMilestone') ?>",
                type: "POST",
                data: $('#milestone').serialize(),
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    location.reload();
                }
            });
        }
    }
})

$(".cancel").click(function() {
    mvalidator.resetForm();
});

var emvalid = $("#editmilestone").validate({
    rules: {
        title: {
            required: true,
        },
        due_date: {
            required: true,
        },
        amount: {
            required: true
        },
    },
    messages: {
        title: {
            required: "Please enter Title",

        },
        due_date: {
            required: "Please enter Date",
        },
        amount: {
            required: "Please enter amount",
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "title")
            error.insertAfter(".mt");
        else if (element.attr("name") == "due_date")
            error.insertAfter(".mddate");
        else if (element.attr("name") == "amount")
            error.insertAfter(".mamt");
    },
    submitHandler: function(form) {
        $('#editmile').html('sending..');
        var url = $('#url_id').val();
        $.ajax({
            url: "<?= $this->Url->build('/companies/updateMilestone') ?>",
            type: "POST",
            data: $('#editmilestone').serialize(),
            dataType: "json",
            success: function(response) {
                location.reload();
            }
        });
    }
})

$(".cancel").click(function() {
    emvalid.resetForm();
});
</script>
<script type="text/javascript">
// getEdit data
function passValue(type, id) {
    $.ajax({
        type: 'GET',
        url: "<?= $this->Url->build('/companies/milesaction/'); ?>" + type + '/' + id,
        beforeSend: function() {
        },
        success: function(data) {
            if (type == 'edit') {
            // if (type == 'edit' || type == 'copy') {
                var response = $.parseJSON(data);

                var d = response.due_date.split('-');
                var date = d[1] + '/' + d[2] + '/' + d[0];

                // Add month/year to title 
                var title = response.title; 
                // if (response.milestone_month_year) { 
                //   title += ' ' + response.milestone_month_year; 
                // }

                $("#title").val(title);
                $("#due_date").val(date);
                $("#amount").val(response.amount);
                $("#mile_id").val(response.id);
            } else {
                // document.getElementById("rowm" + id).remove();
                // // document.getElementById("m_"+id).remove();
                // document.getElementById("rowtm" + id).remove();
                location.reload();
            }

        }
    });

}

function mlabel(id) {
    var count = document.getElementsByClassName("rowtm" + id).length;
    for (var i = 0; i < count; i++) {
        var x = document.getElementsByClassName("rowtm" + id)[i];
        if (x.style.display === "none") {
            x.style.display = "";
            document.getElementById("lm" + id).innerHTML = '<i class="fa fa-chevron-up"></i>';
        } else {
            x.style.display = "none";
            document.getElementById("lm" + id).innerHTML = '<i class="fa fa-chevron-down"></i>';
        }
    }

}

function changeStatus(type, l, id) {
    var val = $('.mstatus').val();

    var url = $('#url_id').val();

    $.ajax({
        url: "<?= $this->Url->build('/companies/status/') ?>" + id + '/' + val + '/' + type,
        method: 'GET',
        success: function(returnData) {
            location.reload();
        }
    });
}
</script>
<script>
//add form
var validator = $("#payment").validate({
    rules: {
        description: {
            required: true,
        },
        payment_date: {
            required: true,
        },
        receive_amt: {
            required: true
        },
    },
    messages: {
        description: {
            required: "Please enter description",

        },
        payment_date: {
            required: "Please enter Date",
        },
        receive_amt: {
            required: "Please enter amount",
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "description")
            error.insertAfter(".ades");
        else if (element.attr("name") == "payment_date")
            error.insertAfter(".pdate");
        else if (element.attr("name") == "receive_amt")
            error.insertAfter(".ramt");
    },
    submitHandler: function(form) {
        $('#savepayment').html('sending..');
        var url = $('#url_id').val();
        $.ajax({
            url: "<?= $this->Url->build('/companies/addPayment') ?>",
            type: "POST",
            data: $('#payment').serialize(),
            dataType: "json",
            success: function(response) {
                $('#savepayment').html('Add to Payment History');
                document.getElementById("payment").reset();
                $(".close").click();

                var html = '<tr id="rowp' + response.id + '">';
                html += '<td>' + response.description + '</td>';
                html += '<td>' + response.payment_date + '</td>';
                html += '<td>$' + response.receive_amt + '</td>';
                html +=
                    '<td><select name="mstatus" class="form-control mstatus" onchange="changeStatus(\'payment\',\'' +
                    url + '\',' + response.id +
                    ')"><option value="Billed">Billed</option><option value="Paid">Paid</option><option value="Estimated">Estimated</option></select></td>';
                html +=
                    '<td><a href="#" class="icon" data-toggle="modal" data-target="#edit_payment" onclick="passPayment(\'edit\',' +
                    response.id +
                    ')"> <i class="fa fa-pencil-alt"></i> </a><a href="#" class="icon" onclick="passPayment(\'delete\',' +
                    response.id + ')"> <i class="fa fa-trash-alt"></i> </a></td>';

                $('#payment_data').prepend(html);

                var html = '<input type="hidden" name="ptid[]" id="p_' + response.id +
                    '" value="' + response.id + '">';
                $('#pro_data').prepend(html);
            }
        });
    }
})

$(".cancel").click(function() {
    validator.resetForm();
});

var editvalidator = $("#editpayment").validate({
    rules: {
        description: {
            required: true,
        },
        payment_date: {
            required: true,
        },
        receive_amt: {
            required: true
        },
    },
    messages: {
        description: {
            required: "Please enter description",

        },
        payment_date: {
            required: "Please enter Date",
        },
        receive_amt: {
            required: "Please enter amount",
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "description")
            error.insertAfter(".des");
        else if (element.attr("name") == "payment_date")
            error.insertAfter(".pdate");
        else if (element.attr("name") == "receive_amt")
            error.insertAfter(".ramt");
    },
    submitHandler: function(form) {
        $('#edtpayment').html('sending..');
        var url = $('#url_id').val();
        $.ajax({
            url: "<?= $this->Url->build('/companies/updatePayment') ?>",
            type: "POST",
            data: $('#editpayment').serialize(),
            dataType: "json",
            success: function(response) {
                $('#edtpayment').html('Update Payment History');
                $(".close").click();
                document.getElementById("rowp" + response.id).remove();

                var html = '<tr id="rowp' + response.id + '">';
                html += '<td>' + response.description + '</td>';
                html += '<td>' + response.payment_date + '</td>';
                html += '<td>$' + response.receive_amt + '</td>';
                html +=
                    '<td><select name="mstatus" class="form-control mstatus" onchange="changeStatus(\'payment\',\'' +
                    url + '\',' + response.id +
                    ')"><option selected hidden value='+ response.status +'>'+ response.status +'</option><option value="Billed">Billed</option><option value="Paid">Paid</option><option value="Estimated">Estimated</option></select></td>';
                html +=
                    '<td><a href="#" class="icon" data-toggle="modal" data-target="#edit_payment" onclick="passPayment(\'edit\',' +
                    response.id +
                    ')"> <i class="fa fa-pencil-alt"></i> </a><a href="#" class="icon" onclick="passPayment(\'delete\',' +
                    response.id + ')"> <i class="fa fa-trash-alt"></i> </a></td>';

                $('#payment_data').prepend(html);
            }
        });
    }
})

$(".cancel").click(function() {
    editvalidator.resetForm();
});
</script>
<script type="text/javascript">
// getEdit data
function passPayment(type, id) {
    $.ajax({

        type: 'GET',
        url: "<?= $this->Url->build('/companies/paymentsaction/'); ?>" + type + '/' + id,

        beforeSend: function() {

        },
        success: function(data) {
            if (type == 'edit') {
                var response = $.parseJSON(data);

                var d = response.payment_date.split('-');
                var date = d[1] + '/' + d[2] + '/' + d[0];
                $("#description").val(response.description);
                $("#payment_date").val(date);
                $("#receive_amt").val(response.receive_amt);
                $("#payment_id").val(response.id);
            } else {
                document.getElementById("rowp" + id).remove();
                document.getElementById("p_" + id).remove();
            }

        }
    });
}

function taskValue(id) {
    var input = $('<input name="milestone_id" type="hidden" value="' + id + '">');
    $('#task_form').append(input);
}
</script>

<script>
$('.client').keyup(function() {
    var str = $(this).val();
    if (str != '') {

        $("#tags").autocomplete({
            source: "<?= $this->Url->build('/clients/listAll/') ?>" + str,
            response: function(event, ui) {

                // ui.content is the array that's about to be sent to the response callback.
                if (ui.content === null) {
                    console.log(ui);
                    $("#tags-error-empty").show();
                    $("#tags-error-empty").html('Client Does Not Exist');

                } else {
                    $("#tags-error-empty").hide();
                }
            }
        });

    }
});
</script>

<script>
//add form
var clientvalidator = $("#clients").validate({
    rules: {
        name: {
            required: true,
        }
    },
    messages: {
        name: {
            required: "Please enter name",

        },

    },
    errorPlacement: function(error, element) {

        if (element.attr("name") == "name")
            error.insertAfter(".clname");

    },
    submitHandler: function(form) {
        $('#saveclient').html('sending..');
        $.ajax({
            url: "<?= $this->Url->build('/clients/add') ?>",
            type: "POST",
            data: $('#clients').serialize(),
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (response == 1) {
                    $('#saveclient').html('Save Client');
                    document.getElementById("clients").reset();
                     $("#add_client").modal("hide");  //added 25 july
                    $(".close").click();
                } else {
                    $('#saveclient').html('Save Client');
                    location.reload();
                }
            }
        });
    }
})

$(".cancel").click(function() {
    clientvalidator.resetForm();
});
</script>

<script>
if ($("#project").length > 0) {
    var prvalid = $("#project").validate({
        rules: {
            project_name: {
                required: true,
            },
            client_name: {
                required: true,

            },
            awarded_on: {
                required: true,
            },
            due_date: {
                required: true,
            },
            amount: {
                required: true,
            },
            project_manager_id: {
                required: true,

            },
            tech_lead_id: {
                required: true,
            },
            bd_id: {
                required: true,
            },
            resources: {
                required: true,
            },
        },
        messages: {
            project_name: {
                required: 'Please enter project name',
            },
            client_name: {
                required: 'Please enter client name',

            },
            awarded_on: {
                required: 'Please enter date',
            },
            due_date: {
                required: 'Please enter due date',
            },
            amount: {
                required: 'Please enter amount',
            },
            project_manager_id: {
                required: 'Please select Manager',

            },
            tech_lead_id: {
                required: 'Please select Tech lead',
            },
            bd_id: {
                required: 'Please select BD member',
            },
            resources: {
                required: 'Please select resources',
            },
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "project_name")
                error.insertAfter(".pname");
            else if (element.attr("name") == "client_name")
                error.insertAfter(".cname");
            else if (element.attr("name") == "awarded_on")
                error.insertAfter(".award");
            else if (element.attr("name") == "due_date")
                error.insertAfter(".ddate");
            else if (element.attr("name") == "amount")
                error.insertAfter(".amt");
            else if (element.attr("name") == "project_manager_id")
                error.insertAfter(".pmgr");
            else if (element.attr("name") == "tech_lead_id")
                error.insertAfter(".tl");
            else if (element.attr("name") == "bd_id")
                error.insertAfter(".bd");
            else if (element.attr("name") == "resources")
                error.insertAfter(".res");
        }
    })


}
</script>


<script>
//add form
var tmvalid = $("#task_form").validate({
    rules: {
        task: {
            required: true,
        },

    },
    messages: {
        task: {
            required: "Please enter Task",

        },


    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "task")
            error.insertAfter(".mtk");

    },
    submitHandler: function(form) {
        $('#savetask').html('sending..');
        var url = $('#url_id').val();
        $.ajax({
            url: "<?= $this->Url->build('/companies/addTask') ?>",
            type: "POST",
            data: $('#task_form').serialize(),
            dataType: "json",
            success: function(response) {
                $('#savetask').html('Save Task');
                document.getElementById("task_form").reset();
                $(".close").click();

                var html = '<tr class="active" id="rowt' + response.id + '"><td></td>';
                html += '<td>' + response.task + '</td>';
                html += '<td>' + response.due_date + '</td>';
                html += '<td>-</td>';
                html +=
                    '<td><select name="mstatus" class="form-control mstatus" onchange="changeStatus(\'tasks\',\'' +
                    url + '\',' + response.id +
                    ')"><option value="Yet to start">Yet to start</option><option value="Inprogress">In progress</option><option value="Completed">completed</option></select></td>';
                html +=
                    '<td><a href="#" class="icon" data-toggle="modal" data-target="#edit_task" onclick="passtaskValue(\'edit\',' +
                    response.id +
                    ')"> <i class="fa fa-pencil-alt"></i> </a><a href="#" class="icon delete-milestone" data-id="' +
                    response.id + '" onclick="passtaskValue(\'delete\',' + response.id +
                    ')"> <i class="fa fa-trash-alt"></i> </a></td>';

                $('#rowtm' + response.milestone_id).prepend(html);



            }
        });
    }
})

$(".cancel").click(function() {
    tmvalid.resetForm();
});
var emvalid = $("#edittask_form").validate({
    rules: {
        task: {
            required: true,
        },


    },
    messages: {
        task: {
            required: "Please enter Task",

        },


    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "task")
            error.insertAfter(".mt");

    },
    submitHandler: function(form) {
        $('#edittask').html('sending..');
        var url = $('#url_id').val();
        $.ajax({
            url: "<?= $this->Url->build('/companies/updateTask') ?>",
            type: "POST",
            data: $('#edittask_form').serialize(),
            dataType: "json",
            success: function(response) {
                $('#edittask').html('Update Task');
                $(".close").click();
                document.getElementById("rowt" + response.id).remove();

                var html = '<tr class="active" id="rowt' + response.id + '"><td></td>';
                html += '<td>' + response.task + '</td>';
                html += '<td>' + response.due_date + '</td>';
                html += '<td>-</td>';
                html +=
                    '<td><select name="mstatus" class="form-control mstatus" onchange="changeStatus(\'tasks\',\'' +
                    url + '\',' + response.id +
                    ')"><option value="Yet to start">Yet to start</option><option value="Inprogress">In progress</option><option value="Completed">completed</option></select></td>';
                html +=
                    '<td><a href="#" class="icon" data-toggle="modal" data-target="#edit_task" onclick="passtaskValue(\'edit\',' +
                    response.id +
                    ')"> <i class="fa fa-pencil-alt"></i> </a><a href="#" class="icon delete-milestone" data-id="' +
                    response.id + '" onclick="passtaskValue(\'delete\',' + response.id +
                    ')"> <i class="fa fa-trash-alt"></i> </a></td>';

                $('#rowtm' + response.milestone_id).prepend(html);
            }
        });
    }
})
$(".cancel").click(function() {
    emvalid.resetForm();
});
</script>
<script type="text/javascript">
// getEdit data
function changeStatusVal(id, dast, type) {
    let val = dast.value;
    $.ajax({
        url: '<?= $this->Url->build(['controller' => 'companies', 'action' => 'status']) ?>' + '/' + id + '/' +
            val + '/' + type,
        method: 'GET',
        success: function(returnData) {
            location.reload();
        }
    });
}

function passtaskValue(type, id) {
    $.ajax({

        type: 'GET',
        url: "<?= $this->Url->build('/companies/tasksaction/'); ?>" + type + '/' + id,

        beforeSend: function() {

        },
        success: function(data) {
            if (type == 'edit') {
                var response = $.parseJSON(data);

                if (response.due_date != '1800-01-01') {
                    var d = response.due_date.split('-');
                    var date = d[1] + '/' + d[2] + '/' + d[0];
                } else {
                    date = '';
                }
                $("#task").val(response.task);
                allMilestone
                $("#task_due_date").val(date);
                $("#milestone_id").val(response.milestone_id);
                $("#task_id").val(response.id);
            } else {
                document.getElementById("rowt" + id).remove();
            }

        }
    });
}

// View all milestone

function allMilestone(id) {
    let mile = document.querySelector('#mile').value;
    $.ajax({
        type: 'GET',
        data: {
            mile: mile
        },
        url: '<?= $this->Url->build("/companies/allMilestone/") ?>' + id,
        success: function(data) {
            let mileData = JSON.parse(data);
            // console.log(mileData);
            let row = '';
            $('#table_data').html("");

            row += `<thead>
                  <tr>
                    <th>#</th>
                    <th style="width:500px">Title</th>
                    <th>Due_Date</th>
                    <th>Amount</th>
                    <th>status</th>
                    <th>Action</th>
                  </tr>
                </thead>          
          `;

            mileData.forEach(element => {
                // console.log(element.title);
                // console.log(element.due_date);
                // console.log(element.amount);
                // console.log(element.status);

                row += `<tr>
                                    <td>
                                      <label class="labels" id="lm${element.id}" onclick="mlabel(${element.id})"><i class="fa fa-chevron-up"></i></label>
                                      <input type="checkbox" name="milestoneOne" id="${element.id}" data-toggle="toggle">
                                    </td>
                                    <td>${element.title}</td>
                                    <td>
                                        ${element.due_date}
                                    </td>
                                    <td>
                                        ${element.amount}
                                    </td>
                            
                                    <td>
                                    
                                    <select name="changeStatus" id="changeValue" class="form-control" onchange="changeStatusVal(${element.id},this,'miles')">
                                    <option value="Yet to start" ${element.status == 'Yet to start' ? 'selected' : '' } >Yet to start</option>
                                    <option value="Inprogress" ${element.status == 'Inprogress' ? 'selected' : '' }>In progress</option>
                                    <option value="Completed" ${element.status == 'Completed' ? 'selected' : '' } >completed</option>
                                    </select>

                                    </td>
                                
                                    <td>
                                    <a href="#" class="icon mtask" data-toggle="modal" data-target="#add_task" onclick="taskValue(${element.id})" title="Add Task"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="icon" data-toggle="modal" data-target="#edit_milestone" onclick="passValue('edit',${element.id})"> <i class="fa fa-pencil-alt"></i> </a>
                                    <a href="#" class="icon" onclick="passValue('delete',${element.id})"> <i class="fa fa-trash-alt"></i> </a>
                                    </td>
                                    
                                </tr>`;
            });

            // console.log(row);
            $('#table_data').html(row);
        }
    });
}

function allResources(id, value) {
    // console.log(id, value);

    $.ajax({
        url: "<?= $this->Url->build('/companies/allResources') ?>",
        method: "GET",
        data: {
            id,
            value
        },
        success: (res) => {

            // $("#allRes").html("");
            let row = "";
        }
    });
}


function upworkDetail() {
    let upworkId = $("#upworkId").val();

    if (upworkId) {
        let url = "<?= $this->Url->build('/companies/milestoneDetails/') ?>";
        window.location.href = `${url}${upworkId}`;
        // console.log(upworkId);
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize Flatpickr
    flatpickr("#datepicker", {
        dateFormat: "m/d/Y", // Set date format
        // You can add more options as needed
    });
</script>