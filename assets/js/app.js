/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../scss/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

var app = {
    fadeOutDelay: 5000,

    init: function() {
        console.log('init');

        document.querySelectorAll('.update_task_form').forEach(form => {
            form.addEventListener('submit', app.handleTaskSubmit);
        });

        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', app.handleClickToDelete);
        });

        app.fadeOutMainAlert();

        document.querySelectorAll('.add_new_task_action').forEach(btn => {
            btn.addEventListener('click', app.handleClickToAddTask);
        });
    },
    fadeOutMainAlert: function() {
        if ($('#main_alert_container .alert')) {
            $('#main_alert_container .alert').fadeOut(app.fadeOutDelay);
        }
    },
    handleTaskSubmit: function(e) {
        // On stoppe la soummission du formulaire
        e.preventDefault();

        var $form = $(e.currentTarget);
        $.ajax({
            type: 'POST',
            url: e.target.getAttribute('action'),
            headers: {"name": "editclientvalidation"},
            data: $form.serialize(),
            dataType:"json",
            success: function(data)
            {
                console.log(data);
                console.log(data.success);
                if(!data.success) {
                    console.log(data.message[0]);
                    $('#error_message_form_' + data.formId).empty();
                    data.message.forEach(message => {
                        $('#error_message_form_' + data.formId).append(message + '<br>');
                    });
                    $('#error_message_form_' + data.formId).show();
                    $('#error_message_form_' + data.formId).fadeOut(app.fadeOutDelay);
                } else {
                    window.location.reload();
                }
            },
            fail: function(e) {
                console.log('Erreur Serveur');
                console.log(e);
            }
        });
    },
    handleClickToDelete: function(e) {
        console.log('handleDeleteClick');
        if (!confirm('Confirmez la suppression.')) {
            e.preventDefault();
        }
    },
    handleClickToAddTask: function(e) {
        // Pré-sélection de l'option
        document.querySelector('#add_task #task_task_list').value = e.target.dataset.listId;
    }
};

document.addEventListener('DOMContentLoaded', app.init);
