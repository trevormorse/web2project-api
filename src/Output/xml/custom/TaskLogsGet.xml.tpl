<response>
    <tasks>
        <?php if (count($data['tasks'])): ?>
        <?php foreach ($data['tasks'] as $task): ?>
        <task>
            <?php foreach ($task as $key => $value): ?>
            <<?php echo $key; ?>><?php echo $value; ?></<?php echo $key ?>>
            <?php endforeach; ?>
        </task>
        <?php endforeach; ?>
        <?php endif; ?>
    </tasks>
    <success><?php echo $data['success']; ?></success>
</response>
