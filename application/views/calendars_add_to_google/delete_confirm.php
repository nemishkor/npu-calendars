<div class="uk-alert uk-alert-warning">
    Ви впевнені що бажаєте видалити календар "<?php echo ($data['calendar']['name']) ? $data['calendar']['name'] : '[немає назви]'; ?>" з вашого облікового запису Google ?
    <form action="/calendars/add_to_google" class="uk-form uk-margin-top">
        <input type="hidden" name="id" value="<?php echo $data['calendar']['id']; ?>">
        <input type="hidden" name="task" value="delete_confirm">
        <button class="uk-button uk-button-danger">Видалити остаточно</button>
    </form>
</div>