<?php $session = new \Cake\Http\Session();
$userSession = $session->read('data');
$role = $userSession['role'];
?>
<section class="page page-dashboard">
    <!-- PAGE-TITLE -->
    <div class="page-title skin-light">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <div class="heading ft-secondary">
                        <span class="icon"><i class="fa fa-project-diagram"></i></span>Project List
                    </div>
                </div>
                <div class="col-6">
                    <?php if (($userSession['role'] != 3) || ($userSession['role'] == 3 && array_intersect($userSession['role_name'], array(10,4,13)))) {
                    ?>
                    <div class="actions-ctrl text-md-right">
                        <?= $this->Html->link('<i class="fa fa-plus"></i><span>Add New Project </span>', '/add-project', ['class' => 'v-btn v-btn-secondary', 'escape' => false]); ?>
                    </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
    <!-- PAGE TAB -->
    <div class="page-tab">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="v-tab">
                        <?php if ($role == 3) { ?>
                        <li class="active">
                            <?= $this->Html->link('My Projects(' . $my . ')', '/my-project', ['class' => '']); ?>
                        </li>
                        <?php }
                        if (($userSession['role'] != 3) || (($userSession['role'] == 3) && (array_intersect($userSession['role_name'], array(10))))) { ?>
                        <li>
                            <?= $this->Html->link('All Projects(' . $count . ')', '/list-project', ['class' => '']); ?>
                        </li>
                        <li>
                            <?= $this->Html->link('Active Projects(' . $active . ')', '/active-project', ['class' => '']); ?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- PAGE-CONTENT -->
    <div class="page-content">
        <div class="container">
        <?php if (array_intersect($userSession['role_name'], array(4, 6, 9, 10))) { ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="adon-group form-group">
                        <!--    <span class="icon icon-light ft-primary"><i class="fa fa-filter"></i></span> -->
                        <select name="sortBy" id="sortBy" class="form-control">
                            <option value="all">All</option>
                            <option value="project-manager"
                                <?= $projectType == "project-manager" ? "selected" : null ?>>
                                Project Manager</option>
                            <option value="bde" <?= $projectType == "bde" ? "selected" : null ?>>BDE</option>
                            <option value="tech-lead" <?= $projectType == "tech-lead" ? "selected" : null ?>>Tech Lead
                            </option>
                            <option value="resource" <?= $projectType == "resource" ? "selected" : null ?>>Resource
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="adon-group form-group">
                        <!--    <span class="icon icon-light ft-primary"><i class="fa fa-filter"></i></span> -->
                        <select name="change" id="change" class="form-control" onchange="changeActivation()">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="adon-group form-group">
                        <!--    <span class="icon icon-light ft-primary"><i class="fa fa-filter"></i></span> -->
                        <select name="" id="proDetails" class="form-control" onchange="projectDetails()">
                            <option value="Pending" selected>Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        
        <?php } ?>

            <!-- TABLE -->
            <div class="row">
                <div class="col-md-12">
                    <!-- <table class="table table-light nowrap table-sm" id="example1" style="width:100%"> -->
                    <table id="example1" style="width:100%" class="table table-light nowrap table-sm  block">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project Name</th>
                                <th>Client</th>
                                <th>Project Manager</th>
                                <th>Due Date</th>
                                <th>OD</th>
                                <!-- <th>Due</th> -->
                                <?php if (array_intersect($userSession['role_name'], array(4, 6, 9, 10))) { ?>
                                <th>Amount</th> 
                                <th>Paid</th> 
                                <th>BH</th>
                                <th>TH</th>
                                <th>AH</th>
                                <th>Active</th>
                                <th>Status</th>
                                <th>Action</th>
                                <?php } else { ?>
                                <th>Status</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            $hasRole13 = in_array(13, $userSession['role_name']);
                            foreach ($projects as $p) :
                                if (!$hasRole13 && $p['source'] === 'Expertal') {
                                    continue;
                                }
                                if ($p['active'] == 1) :
                            ?>
                            <tr id="tr<?= $p['id'] ?>">
                                <td><?= $i; ?></td>
                                <?php if (array_intersect($userSession['role_name'], array(4, 6, 9, 10))) { ?>
                                <td><?= $this->Html->link(substr($p['project_name'], 0, 20), '/edit-project/' . $p['id'], ['class' => 'link']); ?>
                                </td>
                                <?php } else {
                                            echo '<td>' . $p['project_name'] . '</td>';
                                        } ?>
                                <td><?= $p['client']; ?></td>
                                <td><?= $p['project_manager']; ?></td>
                                <td><?= $p['due_date']; ?></td>
                                <td><?php if ($p['overdue'] > 0) { ?><span class="badge badge-danger" title="Overdue"
                                        style="padding: .85em .84em;"><?= $p['overdue']; ?></span><?php } else echo '-'; ?>
                                </td>
                                <!-- <td><?php if ($p['due'] > 0) { ?><span title="due" class="badge badge-warning"
                                        style="padding: .85em .84em;"><?= $p['due']; ?></span><?php } else echo '-'; ?>
                                </td> -->
                                <?php if (array_intersect($userSession['role_name'], array(4, 6, 9, 10))) { ?>
                                <td>$<?= round($p['pm_amount']); ?></td>
                                <td>$<?= round($p['paid']); ?></td>
                                <td>
                                    <?php
                                    echo is_numeric($p['budget'])
                                        ? round($p['budget'])
                                        : $p['budget'];
                                    ?>
                                </td>
                                <td><?= round($p['actual_hours']); ?></td>
                                <td><?= round($p['allocated_hours']); ?></td>
                                <td>
                                    <input class="tgl tgl-light" id="checkType<?= $p['id']; ?>" type="checkbox"
                                        value="<?php echo $p['active']; ?>"
                                        <?= $p['active'] == '1' ? 'checked' : '' ?> />
                                    <label class="tgl-btn" for="<?= $p['id']; ?>"
                                        onclick="changeActivationStatus(<?= $p['id'] ?>,<?php echo $p['active'] ?>)"></label>
                                </td>
                                <td>
                                    <select name="" class="form-control input-sm" id="comPenStatus<?= $p['id'] ?>"
                                        onchange="changeStatus(<?= $p['id']; ?>,'<?= $p['status'] ?>','project')">
                                        <option value="Completed"
                                            <?php if ($p['status'] == "Completed") echo 'selected'; ?>>Completed
                                        </option>
                                        <option value="Pending"
                                            <?php if ($p['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    </select>
                                </td>
                                <td>
                                    <?= $this->Html->link('<i class="fa fa-pencil-alt"></i>', '/edit-project/' . $p['id'], ['class' => 'icon ft-primary icon-sm', 'escape' => false]); ?>

                                    <!-- <a href="" data-toggle="modal" data-id="<?= $p['id']; ?>" data-target="#confirm"
                                        data-type="entry" class="icon icon-sm delete" data-url="<?= WEBURL; ?>">
                                        <i class="fa fa-archive"></i>
                                    </a> -->
                                    <a href="#" onclick="deleteProject(<?= $p['id']; ?>)" class="icon icon-sm delete">
                                        <i class="fa fa-archive"></i>
                                    </a>
                                </td>
                                <?php } else {
                                            echo '<td>' . $p['status'] . '</td>';
                                        } ?>
                            </tr>
                            <?php $i++;
                                endif;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="confirm">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <input type="hidden" name="p_url" id="p_url" value="">
                <!-- Modal body -->
                <form id='delete-data'>
                    <input type="hidden" name="p_id" id="p_id" value="">

                </form>
                <div class="modal-body no-padding">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="widget">
                                    <div class="widget-content">
                                        <h2>Do You Want to Archive this Project?<span class="fw-600 name"></span>?</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="v-btn v-btn-base" data-dismiss="modal">Cancel</button>
                    <button type="button" id="deleteConfirm" class="v-btn v-btn-primary"
                        data-dismiss="modal">Yes</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
$('#sortBy').on('change', function() {
    var manager_id = $(this).val();
    var target_url = "<?= $this->Url->build('/companies/myProject/') ?>" + manager_id;
    if (target_url != null) {
        window.location.href = target_url;
    } else {
        console.log("not null");
        window.location.href = "<?= $this->Url->build('/companies/myProject/all-project') ?>"
    }
});


function deleteProject(id) {
    //     // console.log(id);
    let condition = false;
    if (confirm("Do You Want to Archive this Project?")) condition = true;
    else condition = false;

    if (condition) {
        $.ajax({
            url: "<?= $this->Url->build('/Companies/deleteProject/') ?>" + id,
            method: "GET",
            success: function(res) {
                if (res == 1) $(`#tr${id}`).removeAttr("style").hide();
            },
        });
    }
}

// Status Change
function changeStatus(id, val, type) {
    if (val == 'Completed') {
        val = 'Pending';
    } else {
        val = 'Completed';
    }

    // console.log(id, val, type);
    url = '<?= $this->Url->build('/companies/status/') ?>' + id + '/' + val + '/' + type;
    // console.log(url);
    $.ajax({
        url: url,
        method: 'GET',
        success: function(returnData) {
            // location.reload();
            if (data == 1 && val == `Completed`)
                $(`#comPenStatus${id} [value=${val}]`).attr('selected', 'true');
            else if (data == 1 && val == `Pending`)
                $(`#comPenStatus${id} [value=${val}]`).attr('selected', 'true');
        }
    });
}

function changeActivationStatus(id, status) {
    if (status == 1) {
        status = 0;
    } else {
        status = 1;
    }
    $.ajax({
        url: "<?= $this->Url->build('/companies/updateActive/'); ?>" + id + '/' + status,
        method: 'GET',
        beforeSend: function() {},
        success: function(data) {
            if (data == 1 && status == 0)
                $(`#checkType${id}`).prop('checked', false);
            else if (data == 1 && status == 1)
                $(`#checkType${id}`).prop('checked', true);
            else
                1
            // location.reload();
        }
    });
}

//ActichangeActivationStatus').on('change', function(e) {
//     e.stopPropogation();
// });

// Active or Inactive status

function changeActivation() {
    let val = document.querySelector('#change').value;
    $.ajax({
        type: 'GET',
        url: '<?= $this->Url->build("/companies/changeActivation/") ?>' + val,
        data: {
            val: val
        },
        success: function(data) {
            let projectData = JSON.parse(data);
            // console.log(projectData);
            let row = '';
            $('table tbody').html("");

            let x = 1;
            projectData.forEach(element => {
                if (element.active == 0) {

                    row += `<tr id="tr${element.id}">
                                    <td>${x++}</td>
                                    <td>
                                        <a href="<?= $this->Url->build("/edit-project/") ?>${element.id}">${element.project_name.substr(0,19)}</a>
                                    </td>
                                    <td>
                                        ${element.client}
                                    </td>
                                    <td>
                                        ${element.project_manager}
                                    </td>
                                    <td>
                                        ${element.due_date}
                                    </td>
                                    <td>
                                     ${element.overdue > 0 ? `<span class="badge badge-danger" title="Overdue" style="padding: .85em .84em;"> 
                                        ${element.overdue} </span>`  :  '-'} 
                                    </td>
                                    <td>
                                        $${element.amount}
                                    </td>    
                                    <td>
                                        $${element.paid}
                                    </td> 
                                    <td>${element.budget}</td>
                                    <td>${element.actual_hours}</td>
                                    <td>${element.allocated_hours}</td>      
                                    <td>
                                        <input class="tgl tgl-light" onclick="changeActivationStatus(${element.id},${element.active})" id="${element.id}" type="checkbox" value="${element.active}" ${element.active == '1' ? 'checked' : '' } />
                                        <label class="tgl-btn" for="${element.id}"></label>
                                    </td>  
                                    <td>
                                        <select name="" class="form-control input-sm" id="comPenStatus${element.id}" onchange="changeStatus(${element.id},'${element.status}','project')">
                                        <option value="Completed" ${element.status == "Completed" ? 'selected' : ''}>Completed</option> 
                                        <option value="Pending" ${element.status ==  'Pending' ? 'selected' : '' }>Pending</option>
                                        </select>
                                    </td>
                                    <td>
                                    <a href="<?= $this->Url->build('/edit-project/') ?>${element.id}" class ='icon ft-primary icon-sm'><i class="fa fa-pencil-alt"></i></a>
                                        
                                    <a href="#" onclick="deleteProject(${element.id})" class="icon icon-sm delete">
                                        <i class="fa fa-archive"></i>
                                    </a>
                                        </a>
                                    </td>                        
                                `;
                } else {

                    row += `<tr>
                                    <td>${x++}</td>
                                    <td>
                                        <a href="<?= $this->Url->build("/edit-project/") ?>${element.id}">${element.project_name.substr(0,19)}</a>
                                    </td>
                                    <td>
                                        ${element.client}
                                    </td>
                                    <td>
                                        ${element.project_manager}
                                    </td>
                                    <td>
                                        ${element.due_date}
                                    </td>
                                    <td>
                                     ${element.overdue > 0 ? `<span class="badge badge-danger" title="Overdue" style="padding: .85em .84em;"> 
                                        ${element.overdue} </span>`  :  '-'} 
                                    </td> 
                                    <td>
                                        $${element.amount}
                                    </td>    
                                    <td>
                                        $${element.paid}
                                    </td> 
                                    <td>${element.budget}</td>
                                    <td>${element.actual_hours}</td>
                                    <td>${element.allocated_hours}</td>      
                                    <td>
                                        <input class="tgl tgl-light" onclick="changeActivationStatus(${element.id},${element.active})" id="${element.id}" type="checkbox" value="${element.active}" ${element.active == '1' ? 'checked' : '' } />
                                        <label class="tgl-btn" for="${element.id}"></label>
                                    </td>  
                                    <td>
                                        <select name="" class="form-control input-sm" id="comPenStatus${element.id}" onchange="changeStatus(${element.id},'${element.status}','project')">
                                        <option value="Completed" ${element.status == "Completed" ? 'selected' : ''}>Completed</option> 
                                        <option value="Pending" ${element.status ==  'Pending' ? 'selected' : '' }>Pending</option>
                                        </select>
                                    </td>
                                    <td>
                                    <a href="<?= $this->Url->build('/edit-project/') ?>${element.id}" class ='icon ft-primary icon-sm'><i class="fa fa-pencil-alt"></i></a>
                                        <a href="" data-toggle="modal" data-id="${element.id}" data-target="#confirm" data-type="entry" class="icon icon-sm delete" data-url="<?= WEBURL; ?>">
                                        <i class="fa fa-archive"></i>
                                        </a>
                                    </td>                        
                                `;
                }
            });
            $('table tbody').html(row);
        }
    });
}

// Pending and Completed status

function projectDetails() {

    let proDetails = document.querySelector("#proDetails").value;

    $.ajax({
        type: 'GET',
        url: '<?= $this->Url->build("/companies/projectDetails/") ?>' + proDetails,
        data: {
            proDetails: proDetails
        },
        success: function(data) {
            let projectData = JSON.parse(data);
            // console.log(projectData);
            let row = '';
            $('table tbody').html("");

            let x = 1;
            projectData.forEach(element => {
                if (element.status == "Pending") {

                    row += `<tr>
                                    <td>${x++}</td>
                                    <td>
                                        <a href="<?= $this->Url->build("/edit-project/") ?>${element.id}">${element.project_name.substr(0,19)}</a>
                                    </td>
                                    <td>
                                        ${element.client}
                                    </td>
                                    <td>
                                        ${element.project_manager}
                                    </td>
                                    <td>
                                        ${element.due_date}
                                    </td>
                                    <td>
                                     ${element.overdue > 0 ? `<span class="badge badge-danger" title="Overdue" style="padding: .85em .84em;"> 
                                        ${element.overdue} </span>`  :  '-'} 
                                    </td> 
                                    <td>
                                        $${element.amount}
                                    </td>    
                                    <td>
                                        $${element.paid}
                                    </td> 
                                    <td>${element.budget}</td>
                                    <td>${element.actual_hours}</td>
                                    <td>${element.allocated_hours}</td>      
                                    <td>
                                        <input class="tgl tgl-light" onclick="changeActivationStatus(${element.id},${element.active})" id="${element.id}" type="checkbox" value="${element.active}" ${element.active == '1' ? 'checked' : '' } />
                                        <label class="tgl-btn" for="${element.id}"></label>
                                    </td>  
                                    <td>
                                        <select name="" class="form-control input-sm" id="comPenStatus${element.id}" onchange="changeStatus(${element.id},'${element.status}','project')">
                                        <option value="Completed" ${element.status == "Completed" ? 'selected' : ''}>Completed</option> 
                                        <option value="Pending" ${element.status ==  'Pending' ? 'selected' : '' }>Pending</option>
                                        </select>
                                    </td>
                                    <td>
                                    <a href="<?= $this->Url->build('/edit-project/') ?>${element.id}" class ='icon ft-primary icon-sm'><i class="fa fa-pencil-alt"></i></a>
                                        <a href="" data-toggle="modal" data-id="${element.id}" data-target="#confirm" data-type="entry" class="icon icon-sm delete" data-url="<?= WEBURL; ?>">
                                        <i class="fa fa-archive"></i>
                                        </a>
                                    </td>                        
                                `;
                } else {

                    row += `<tr>
                                    <td>${x++}</td>
                                    <td>
                                        <a href="<?= $this->Url->build("/edit-project/") ?>${element.id}">${element.project_name.substr(0,19)}</a>
                                    </td>
                                    <td>
                                        ${element.client}
                                    </td>
                                    <td>
                                        ${element.project_manager}
                                    </td>
                                    <td>
                                        ${element.due_date}
                                    </td>
                                    <td>
                                     ${element.overdue > 0 ? `<span class="badge badge-danger" title="Overdue" style="padding: .85em .84em;"> 
                                        ${element.overdue} </span>`  :  '-'} 
                                    </td>
                                    <td>
                                        $${element.amount}
                                    </td>    
                                    <td>
                                        $${element.paid}
                                    </td> 
                                    <td>${element.budget}</td>
                                    <td>${element.actual_hours}</td>
                                    <td>${element.allocated_hours}</td>      
                                    <td>
                                        <input class="tgl tgl-light" onclick="changeActivationStatus(${element.id},${element.active})" id="${element.id}" type="checkbox" value="${element.active}" ${element.active == '1' ? 'checked' : '' } />
                                        <label class="tgl-btn" for="${element.id}"></label>
                                    </td>  
                                    <td>
                                        <select name="" class="form-control input-sm" id="comPenStatus${element.id}" onchange="changeStatus(${element.id},'${element.status}','project')">
                                        <option value="Completed" ${element.status == "Completed" ? 'selected' : ''}>Completed</option> 
                                        <option value="Pending" ${element.status ==  'Pending' ? 'selected' : '' }>Pending</option>
                                        </select>
                                    </td>
                                    <td>
                                    <a href="<?= $this->Url->build('/edit-project/') ?>${element.id}" class ='icon ft-primary icon-sm'><i class="fa fa-pencil-alt"></i></a>
                                        <a href="" data-toggle="modal" data-id="${element.id}" data-target="#confirm" data-type="entry" class="icon icon-sm delete" data-url="<?= WEBURL; ?>">
                                        <i class="fa fa-archive"></i>
                                        </a>
                                    </td>                        
                                `;
                }
            });
            $('table tbody').html(row);
        }
    });

}

$(document).ready(function() {
    $('#example1').DataTable({
        responsive: true,
        scrollX: true,
        "pageLength": 50
    });
});
</script>