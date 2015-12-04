<?php
class system_prism_queue_message implements system_interface_queue_message
{
    private $__tag;
    private $__worker;
    private $__params;
    private $__queueName;

    public function __construct($data)
    {
        $this->__tag = $data['tag'];
        $this->__worker = $data['worker'];
        $this->__params = $data['params'];
        $this->__queueName = $data['queueName'];
    }

    public function get_tag()
    {
        return $this->__tag;
    }

    public function get_worker()
    {
        return $this->__worker;
    }

    public function get_params()
    {
        return $this->__params;
    }

    public function get_queueName()
    {
        return $this->__queueName;
    }

}
