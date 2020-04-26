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
    fadeOutDelay: 7000,

    init: function() {
        // Gestion de l'affichage du formulaire de création d'une nouvelle tâche
        document.querySelectorAll('.add_new_task_action').forEach(btn => {
            btn.addEventListener('click', app.handleClickToAddTask);
        });

        // Ecouteur sur la soumission du formulaire de création d'une nouvelle tâche
        if (document.querySelector('#new_task_form')) {
            document.querySelector('#new_task_form').addEventListener('submit', app.handleTaskSubmit);
        }

        // Ecouteurs sur les formulaires d'édition des tâches
        document.querySelectorAll('.update_task_form').forEach(form => {
            form.addEventListener('submit', app.handleTaskSubmit);
        });

        // Gestion des messages flash (lorsque visibles)
        app.fadeOutMainAlert();
    },
    fadeOutMainAlert: function() {
        // Disparition progressive des messages flash
        if ($('#main_alert_container .alert')) {
            $('#main_alert_container .alert').fadeOut(app.fadeOutDelay);
        }
    },
    handleTaskSubmit: function(e) {
        // On stoppe la soummission du formulaire
        e.preventDefault();

        // Gestion du formulaire task
        var $form = $(e.currentTarget);
        $.ajax({
            type: 'POST',
            url: e.target.getAttribute('action'),
            headers: {"name": "editclientvalidation"},
            data: $form.serialize(),
            dataType:"json",
            success: function(data)
            {
                if(!data.success) {
                    // En cas d'échec
                    if (data.formId) {
                        // Gestion affichage des erreurs des formulaires d'édition
                        $('#error_message_form_' + data.formId).empty();
                        data.message.forEach(message => {
                            $('#error_message_form_' + data.formId).append(message + '<br>');
                        });
                        $('#error_message_form_' + data.formId).show();
                        $('#error_message_form_' + data.formId).fadeOut(app.fadeOutDelay);
                    } else {
                        // Gestion affichage des erreurs du formulaires de création
                        $('#error_message_form').empty();
                        data.message.forEach(message => {
                            $('#error_message_form').append(message + '<br>');
                        });
                        $('#error_message_form').show();
                        $('#error_message_form').fadeOut(app.fadeOutDelay);
                    }
                } else {
                    // En cas de succès
                    window.location.reload();
                }
            },
            fail: function(e) {
                console.log('Erreur Serveur');
                console.log(e);
            }
        });
    },
    handleClickToAddTask: function(e) {
        // Pré-sélection de l'option dans le formulaire de création d'une nouvelle tâche
        document.querySelector('#add_task #task_task_list').value = e.target.dataset.listId;
    }
};

document.addEventListener('DOMContentLoaded', app.init);
