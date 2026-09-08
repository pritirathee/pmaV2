<?php //$session = new \Cake\Http\Session();
if (!empty($projects)) extract($projects[0]);
//$project_id = $session->read('project_id');
?>
<section class="page page-dashboard">
    <!-- PAGE-TITLE -->
    <div class="page-title skin-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="heading ft-secondary">
                        <span class="icon"><i class="fa fa-project-diagram"></i></span>Add New Projects
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="actions-ctrl text-md-right">
                        <?= $this->Html->link('<i class="fa fa-list"></i><span>List Project</span>', '/list-project', ['class' => 'v-btn v-btn-secondary', 'escape' => false]); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PAGE-CONTENT -->
    <div class="page-content">
        <div class="container">
            <!-- PROJECT ADD -->
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="url_id" value="<?= WEBURL; ?>">
                    <?= $this->Form->create(null, array('id' => 'project')) ?>
                    <div class="block">
                        <?= $this->Flash->render() ?>
                        <div class="header">
                            <h4 class="title">Add Project Details</h4>
                        </div>
                        <div class="content ">
                            <div class="row" id="pro_data">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Project Name</label>
                                        <div class="adon-group pname">
                                            <input type="hidden" name="project_id"
                                                value="<?= !empty($projects) ? $id : ''; ?>">
                                            <input type="text" class="form-control" name="project_name"
                                                value="<?= !empty($projects) ? $project_name : ''; ?>" placeholder=""
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Client Name</label>
                                        <div class="adon-group cname">
                                            <input id="tags" class="form-control client" name="client_name"
                                                value="<?= !empty($projects) ? $client : ''; ?>">
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
                                            <input type="number" id="upworkId" class="form-control" name="upwork_ref_id">
                                            <!-- <a href="javascript:void(0)" onclick="upworkDetail()"
                                                class="v-btn  v-btn-primary" style="width:40%">Contract</a> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Awarded On</label>
                                        <div class="adon-group award">
                                            <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                            <input class="datepicker form-control" type="text" placeholder=""
                                                name="awarded_on" autocomplete="off"
                                                value="<?= !empty($projects) ? $award : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Due Date</label>
                                        <div class="adon-group ddate">
                                            <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                            <input class="datepicker form-control" type="text" placeholder=""
                                                name="due_date" value="<?= !empty($projects) ? $due_date : ''; ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Project Type</label>
                                        <div class="adon-group amt">
                                            <span class="icon ft-primary"><i class="fa fa-dollar-sign"></i></span>
                                            <select name="project_type" id="" class="form-control">
                                                <option value="Fixed Price" <?php if (!empty($projects)) {
                                                                                echo ($type == 'Fixed Price') ? 'selected' : '';
                                                                            } ?>>Fixed</option>
                                                <option value="Hourly" <?php if (!empty($projects)) {
                                                                            echo ($type == 'hourly') ? 'selected' : '';
                                                                        } ?>>Hourly</option>
                                            </select>
                                            <input type="text" class="form-control text-right" style="width:70px"
                                                placeholder="$3000" name="amount"
                                                value="<?= !empty($projects) ? $amount : ''; ?>" autocomplete="off"
                                                onkeypress="return isareaNumber(event)">
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

                                                <option value="<?= $m->id; ?>" <?php if (!empty($projects)) {
                                                                                        echo ($project_manager_id == $m->id) ? 'selected' : '';
                                                                                    } ?>><?= $m->name; ?></option>
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

                                                <option value="<?= $m->id; ?>" <?php if (!empty($projects)) {
                                                                                        echo ($tech_lead_id == $m->id) ? 'selected' : '';
                                                                                    } ?>><?= $m->name; ?></option>
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

                                                <option value="<?= $m->id; ?>" <?php if (!empty($projects)) {
                                                                                        echo ($bd_id == $m->id) ? 'selected' : '';
                                                                                    } ?>><?= $m->name; ?></option>
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
                                                <?php if (!empty($projects)) $res = explode(',', $resource); ?>
                                                <?php foreach ($resources as $m) : ?>

                                                <option value="<?= $m->id; ?>" <?php if (!empty($projects)) {
                                                                                        echo (in_array($m->id, $res)) ? 'selected' : '';
                                                                                    } ?>>
                                                    <?= $m->name; ?>
                                                </option>
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
                                                autocomplete="off" value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mt-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="bill" id="">
                                            <label class="form-check-label mx-md-2" for="">Check for Non
                                                Billable</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mt-md-4">
                                        <div class="form-check">
                                            <!-- <i nput type="checkbox" class="form-check-input" name="bill" id="">
                                            <label class="form-check-label mx-md-2" for="">Check for Non
                                                Billable</label> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row-reverse">
                                <div class="form-group" style="margin-top:22px;">
                                    <button type="submit" class="v-btn v-btn-secondary float-right" id="save_project"
                                        <?php if (!empty($projects)) {
                                                                                                                            echo 'disabled="true"';
                                                                                                                        } ?>><span>Save
                                            Project</span></button>
                                </div>
                                <?= $this->Form->end() ?>
                            </div>
                        </div>
                    </div>
                    <div id="projectExtraSections" style="<?php echo empty($projects) ? 'display:none;' : 'display:block;'; ?>">
                        <div class="block">
                            <div class="header">
                                <h4 class="title">Project Milestone <a href="#" data-target="#add_milestone"
                                        data-toggle="modal" class="v-btn v-btn-primary float-right"><i
                                            class="fa fa-plus"></i><span>Add Milestone</span></a></h4>
                                </h4>
                            </div>
                            <div class="content table-responsive">
                                <table class="table table-default" id="table_data">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="width:500px">Title</th>
                                            <th>Due Date</th>
                                            <th>Amount</th>
                                            <th>status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <?php if (!empty($miles)) : ?>
                                    <?php foreach ($miles as $m) : ?>
                                    <tbody id="rowm<?= $m['id']; ?>">

                                        <tr class="active">
                                            <td>
                                                <label class="labels" id="lm<?= $m['id']; ?>"
                                                    onclick="mlabel(<?= $m['id']; ?>)"><i
                                                        class="fa fa-chevron-up"></i></label>
                                                <input type="checkbox" name="milestoneOne" id="m<?= $m['id']; ?>"
                                                    data-toggle="toggle">
                                            </td>
                                            <td><?= $m['title']; ?></td>
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
                                                <a href="#" class="icon" onclick="passValue('delete',<?= $m['id']; ?>)"> <i
                                                        class="fa fa-trash-alt"></i> </a>
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
                                    <?php endforeach; ?>
                                    <?php endif; ?>

                                </table>
                            </div>
                        </div>
                        <div class="block">
                            <div class="header">
                                <h4 class="title">Resources Allocation </h4>
                                <input type="hidden" id="url" value="<?= WEBURL; ?>">
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
                                    <tbody>
                                        <?php if (!empty($projects)) :
                                            $i = 1;
                                            foreach ($resourceList as $rl) : ?>
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
                                            endforeach;
                                        endif; ?>
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
                                            <th></th>
                                            <th style="width:500px;">Description</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payment_data">
                                        <?php if (!empty($payments)) : ?>
                                        <?php foreach ($payments as $p) : ?>

                                        <tr id="rowp<?= $p['id']; ?>">
                                            <td></td>
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
                                                        <?php if ($p['status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                                                    <option value="Estimated"
                                                        <?php if ($p['status'] == 'Estimated') echo 'selected'; ?>>Estimated
                                                    </option>

                                                </select>
                                            </td>
                                            <td>
                                                <a href="#" class="icon" data-toggle="modal" data-target="#edit_payment"
                                                    onclick="passPayment('edit',<?= $p['id']; ?>)"> <i
                                                        class="fa fa-pencil-alt"></i> </a>
                                                <a href="#" class="icon" onclick="passPayment('delete',<?= $p['id']; ?>)">
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
                            <label for="">Company Name</label>
                            <div class="adon-group">
                                <span class="icon ft-primary"><i class="fa fa-building"></i></span>

                                <input type="text" class="form-control" name="company_name" placeholder=""
                                    autocomplete="off">
                                <input type="hidden" name="addClientName" value="123">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Client Name</label>
                            <div class="adon-group clname">
                                <span class="icon ft-primary"><i class="fa fa-user"></i></span>
                                <input type="text" class="form-control" name="client_name" placeholder=""
                                    autocomplete="off">
                                <input type="hidden" name="password" value="password">
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
                                <input type="text" name="contact_no" class="form-control" placeholder="Contact No"
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
                                <input type="hidden" name="project_id" value="<?= !empty($projects) ? $id : ''; ?>">
                                <input type="text" class="form-control" name="title" placeholder="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Due Date</label>
                            <div class="adon-group mddate">
                                <span class="icon ft-primary"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker" name="due_date" placeholder=""
                                    autocomplete="off">
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
                                <input type="hidden" name="project_id" value="<?= !empty($projects) ? $id : ''; ?>">
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
                                <input type="text" class="form-control datepicker" name="payment_date" placeholder=""
                                    autocomplete="off">
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
                <button type="submit" name="submit" class="v-btn v-btn-primary" id="editpayment">Update Payment
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
                var response = $.parseJSON(data);

                var d = response.due_date.split('-');
                var date = d[1] + '/' + d[2] + '/' + d[0];
                $("#title").val(response.title);
                $("#due_date").val(date);
                $("#amount").val(response.amount);
                $("#mile_id").val(response.id);
            } else {
                document.getElementById("rowm" + id).remove();
                // document.getElementById("m_"+id).remove();
                document.getElementById("rowtm" + id).remove();
            }

        }
    });
}


function mlabel(id) {
    var x = document.getElementById("rowtm" + id);
    if (x.style.display === "none") {
        x.style.display = "";
        document.getElementById("lm" + id).innerHTML = '<i class="fa fa-chevron-up"></i>';
    } else {
        x.style.display = "none";
        document.getElementById("lm" + id).innerHTML = '<i class="fa fa-chevron-down"></i>';
    }
}

function isareaNumber(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 190 && charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) return false;
    return true;
}

function changeStatus(type, l, id) {
    var val = $('.mstatus').val();

    // var fullUrl = url + 'companies/status/';

    $.ajax({
        url: "<?= $this->Url->build('/companies/status/'); ?>" + id + '/' + val + '/' + type,
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
                $('#savepayment').html('Add To Payment History');
                document.getElementById("payment").reset();
                $(".close").click();

                var html = '<tr id="rowp' + response.id + '"><td></td>';
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
        $('#editpayment').html('sending..');
        var url = $('#url_id').val();
        $.ajax({
            url: "<?= $this->Url->build('/companies/updatePayment') ?>",
            type: "POST",
            data: $('#editpayment').serialize(),
            dataType: "json",
            success: function(response) {
                $('#editpayment').html('Update Payment History');
                $(".close").click();
                document.getElementById("rowp" + response.id).remove();

                var html = '<tr id="rowp' + response.id + '"><td></td>';
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
        client_name: {
            required: true,
        },

    },
    messages: {
        client_name: {
            required: "Please enter name",

        },
        email: {
            required: "please enter correct email",
        }
    },
    errorPlacement: function(error, element) {

        if (element.attr("name") == "client_name")
            error.insertAfter(".clname");
        else if (element.attr("name") == "email")
            error.insertAfter(".emailclass");
    },
    submitHandler: function(form) {
        $('#saveclient').html('sending..');
        $.ajax({
            url: "<?= $this->Url->build('/clients/add') ?>",
            type: "POST",
            data: $('#clients').serialize(),
            dataType: "json",
            success: function(response) {
                if (response == 1) {
                    $('#saveclient').html('Save Client');
                    document.getElementById("clients").reset();
                      $("#add_client").modal("hide");  //added 25 july
                    $(".close").click();
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
    });
    if (prvalid == true)
        $('#save_project').html('sending..');
    else
        $('#save_project').html('Save Project');
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
                $("#task_due_date").val(date);
                $("#milestone_id").val(response.milestone_id);
                $("#task_id").val(response.id);
            } else {
                document.getElementById("rowt" + id).remove();
            }

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

<script>
$(document).ready(function () {

    $('#save_project').click(function () {

        setTimeout(function () {
            $('#projectExtraSections').show();
        }, 500);

    });

});
</script>