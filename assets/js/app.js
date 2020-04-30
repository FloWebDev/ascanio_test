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
        // Mise en place de MutationObserver qui permettra de sélectionner des éléments APRES chargement du DOM
        // (lors des changements dynamiques du DOM)
        app.observer();

        // Affichage dynamique du formulaire de création des tâches
        document.querySelectorAll('.add_task_action').forEach(btn => {
            btn.addEventListener('click', app.handleClickToDisplayTaskForm);
        });

        // Affichage dynamique du formulaire d'édition d'une tâche
        document.querySelectorAll('.update_task_action').forEach(btn => {
            btn.addEventListener('click', app.handleClickToDisplayTaskForm);
        });

        // Affichage de la modal associée à la suppression d'une tâche
        document.querySelectorAll('.delete_task_action').forEach(btn => {
            btn.addEventListener('click', app.handleClickToDeleteTask);
        });

        // Gestion des messages flash (lorsque visibles)
        app.fadeOutMainAlert();
    },
    observer: function() {
        // @link https://developer.mozilla.org/fr/docs/Web/API/MutationObserver
        // Selectionne le noeud dont les mutations seront observées
        var targetNode = document.querySelector('#content_task_form');

        // Options de l'observateur (quelles sont les mutations à observer)
        var config = { attributes: true, childList: true,subtree: true };

        // Créé une instance de l'observateur lié à la fonction de callback
        var observer = new MutationObserver((mutationsList) => {
            // Fonction callback à éxécuter quand une mutation est observée
            for(var mutation of mutationsList) {
                if (mutation.type == 'childList') {
                    // Si le formulaire des tâches est inséré dans le DOM, on y pose un écouteur sur l'event "submit"
                    if (document.querySelector('#dynamic_task_form')) {
                        document.querySelector('#dynamic_task_form').addEventListener('submit', app.handleTaskSubmit);
                    }
                }
            }
        });

        // Commence à observer le noeud cible pour les mutations précédemment configurées
        observer.observe(targetNode, config);
    },
    fadeOutMainAlert: function() {
        // Disparition progressive des messages flash
        if ($('#main_alert_container .alert')) {
            $('#main_alert_container .alert').fadeOut(app.fadeOutDelay);
        }
    },
    handleClickToDisplayTaskForm: function(e) {
        var taskUrlController = e.target.dataset.taskUrl;

        $.ajax({
            type: 'POST',
            url: taskUrlController,
            data: {},
            dataType: "json",
            success: function(data)
            {
                if (data.form) {
                    // On ajoute la view du formulaire à la DIV prévue à cet effet
                    document.querySelector('#content_task_form').innerHTML = data.form;

                    // Si dataset data-list-id, alors il s'agit d'un formulaire de création d'une tâche
                    // On pré-sélectionne dans ce cas la liste d'appartenance
                    if (e.target.dataset.listId) {
                        document.querySelector('#task #task_task_list').value = e.target.dataset.listId;
                    }
                }
            },
            fail: function(e) {
                console.log('Erreur Serveur');
                console.log(e);
            }
        });
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
                    // En cas d'erreur dans la soumission du formulaire
                    $('#error_message_form').empty();
                    data.message.forEach(message => {
                        $('#error_message_form').append(message + '<br>');
                    });
                    $('#error_message_form').show();
                    $('#error_message_form').fadeOut(app.fadeOutDelay);
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
    handleClickToDeleteTask: function(e) {
        document.querySelector('#content_delete_task h5').textContent = e.currentTarget.dataset.taskList;
        document.querySelector('#content_delete_task p').textContent = e.currentTarget.dataset.taskName;
        document.querySelector('#task_delete_link').href = e.currentTarget.dataset.taskDeleteUrl;
    }
};

document.addEventListener('DOMContentLoaded', app.init);
