<response>
    <contacts>
        <?php if (count($data['contacts'])): ?>
        <?php foreach ($data['contacts'] as $contact_id => $contact_name): ?>
        <contact_id><?php echo $contact_id; ?></contact_id>
        <contact_name><?php echo $contact_name; ?></contact_name>
        <?php endforeach; ?>
        <?php endif; ?>
    </contacts>
    <success><?php echo $data['success']; ?></success>
</response>