function toggleChecks () 
{
    $('.check').each(function() {
        $(this).prop('checked',!$(this).prop('checked'));
    });
}
