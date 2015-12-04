<?php

interface search_interface_segment
{
    public function set($input, $encode='');

    public function reset();

    public function next();

    public function tokenize($input, $encode='');

}
