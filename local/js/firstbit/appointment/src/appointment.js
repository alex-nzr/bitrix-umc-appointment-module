import {Type} from 'main.core';
import 'normalize.css';
import './style.scss';

type ITextFields = {
	name: string;
};

export class Appointment
{
	param: ITextFields = {
		name: 'string',
		time: '12'
	};

	constructor(options = {name: 'def name'})
	{
		this.name = options.name;
	}

	setName(name)
	{
		if (Type.isString(name))
		{
			this.name = name;
		}
	}

	getName()
	{
		return this.name;
	}
}