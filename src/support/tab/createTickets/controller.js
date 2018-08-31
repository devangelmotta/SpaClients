(function(){
	'use strict';

	angular
	.module('support')
	.controller('createTickets', createTickets);

	createTickets.$inject = ['Tickets', 'Clients'];

	function createTickets(Tickets, Clients){
		var ct = this;

		ct.clients = Clients.query();
		ct.newTicket = {};
		ct.saveTicket = saveTicket;

		function saveTicket(){
			Tickets.create(ct.newTicket, function success(response){
				Materialize.toast('Ticket Creado con Éxito!', 5000, 'green');
				ct.newTicket = {};
			}, function error(response){
				Materialize.toast('No se pudo crear el Ticket', 5000, 'red');
			});
		}
	}
})();