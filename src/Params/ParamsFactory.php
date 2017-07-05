<?php namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;


class ParamsFactory {

    public static function get() {

        $params = new DefaultParams;

        $cli = new CLIGetter;
        $paramsCLI = $cli->getParams();

        if (!isset($paramsCLI->debug)) {
            error_reporting(E_ERROR);
        }

        $fs = new FSGetter($paramsCLI);
        $paramsFS = $fs->getParams();

        $params = self::merge($params, $paramsFS);
        $params = self::merge($params, $paramsCLI);

        if (empty($params->server1)) {
            throw new CLIException("A server is required");
        }

        if (
            isset($params->input['source']['server'])
            &&
            isset($params->input['source']['db'])
            &&
            isset($params->input['source']['table'])
        ) {
            $params->input['kind'] = "table";
        }
        elseif (
            isset($params->input['source']['server'])
            &&
            isset($params->input['source']['db'])
        ) {
            $params->input['kind'] = "db";
        } else throw new CLIException("Unkown kind of resources");

        return $params;
    }

    protected function merge($obj1, $obj2) {
        return (object) array_merge((array) $obj1, (array) $obj2);
    }
}
