<?php
class system_mdl_prism_initstep extends dbeav_model
{
    public function createMission($handlar, $params)
    {
        $mission = [
            'state' => 'ready',
            'handlar' => $handlar,
            'params' => $params,
            'create_time' => time(),
            ];
        return $this->save($mission);
    }

    public function getNext()
    {
        return $this->getRow('step_id, handlar, params', ['state'=>'ready'], 'step_id asc');
    }

    public function setStartTime($mission, $time = null)
    {
        if( is_null($time) )
            $time = time();
        $mission['start_time'] = $time;
        return $this->save( $mission );
    }

    public function setComplete($mission, $result, $time = null)
    {
        if( is_null($time) )
            $time = time();

        $mission['complete_time'] = $time;
        $mission['result'] = $result;
        $mission['state'] = 'complete';
        return $this->save( $mission );
    }
}
