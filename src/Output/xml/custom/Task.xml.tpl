<response>
    <task>
        <?php foreach ($data['task'] as $key => $value): ?>
            <?php if ($key == 'task_departments'): ?>
                <<?php echo $key; ?>>
                <?php foreach ($value as $department_id): ?>
                    <task_department><?php echo $department_id; ?></task_department>
                <?php endforeach; ?>
                </<?php echo $key; ?>>
            <?php elseif ($key == 'task_contacts'): ?>
                <<?php echo $key; ?>>
                <?php foreach ($value as $contact_id): ?>
                    <task_contact><?php echo $department_id; ?></task_contact>
                <?php endforeach; ?>
                </<?php echo $key; ?>>
            <?php else: ?>
                <<?php echo $key; ?>><?php echo $value; ?></<?php echo $key; ?>>
            <?php endif; ?>
        <?php endforeach; ?>
    </project>
    <success><?php echo $data['success']; ?></success>
</response>
