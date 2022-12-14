<?php

use Illuminate\Contracts\Routing\ResponseFactory;

if (!function_exists('customApiResponse')) {
    /**
     * Return a new response from the application.
     *
     * eg in controller return customApiResponse($users, 'successfully created', 201)
     * eg in controller return customApiResponse($users, 'error', 500, ['internal server error'])
     * @param  string  $data
     * @param  string  $message
     * @param  string  $errors
     * @param  int     $status
     * @param  array   $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function customApiResponse($data = '', $message = '', $status = 200, $errors = [], array $headers = ['Content-Type' => 'application/json']) {
        /**
         * make sure that we always return errors as an array of errors
         */
        $errorsArray = [];
        if(!is_array($errors)) {
            array_push($errorsArray, $errors);
            $errors = $errorsArray;
        }

        $responseData =  [
            'data'    => $data,
            'message' => $message,
            'errors'  => $errors,
            'status'  => $status
        ];

        $factory = app(ResponseFactory::class);
        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make(json_encode($responseData), $status, $headers);
    }
}