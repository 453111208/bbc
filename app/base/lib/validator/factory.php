<?php

class base_validator_factory {

	/**
	 * Create a new Validator instance.
	 *
	 * @param  array  $data
	 * @param  array  $rules
	 * @param  array  $messages
	 * @return \Illuminate\Validation\Validator
	 */
	public function make(array $data, array $rules, array $messages = array())
	{

		$validator = new base_validator_validator($data,$rules,$messages);

		return $validator;

	}

}
