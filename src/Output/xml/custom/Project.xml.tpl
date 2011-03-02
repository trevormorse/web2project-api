<response>
    <project>
        <?php foreach ($data['project'] as $key => $value): ?>
            <?php if ($key == 'project_departments'): ?>
                <<?php echo $key; ?>>
                <?php foreach ($value as $department_id): ?>
                    <project_department><?php echo $department_id; ?></project_department>
                <?php endforeach; ?>
                </<?php echo $key; ?>>
            <?php elseif ($key == 'project_contacts'): ?>
                <<?php echo $key; ?>>
                <?php foreach ($value as $contact_id): ?>
                    <project_contact><?php echo $contact_id; ?></project_contact>
                <?php endforeach; ?>
                </<?php echo $key; ?>>
            <?php else: ?>
                <<?php echo $key; ?>><?php echo $value; ?></<?php echo $key; ?>>
            <?php endif; ?>
        <?php endforeach; ?>
    </project>
    <success><?php echo $data['success']; ?></success>
</response>
