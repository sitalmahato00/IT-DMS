ter name="content">
<?php
$c = file_get_contents('app/Http/Controllers/Admin/StudyMaterialController.php');
$c = str_replace('Subject::active()', 'Subject::select("id", "subject_name", "subject_code", "semester")->get()', $c);
file_put_contents('app/Http/Controllers/Admin/StudyMaterialController.php', $c);
echo "Fixed - subjects will now load all records\n";
