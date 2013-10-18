dojo.provide('dope.entity.FormContainer');
dojo.require('dope.entity.Form');
dojo.require('dope.layout.BorderContainer');

dojo.declare('dope.entity.FormContainer', [dope.entity.Form, dope.layout.BorderContainer], {
	baseClass: 'dopeForm'
});