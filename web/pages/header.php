<?php

$renderer = \Nick::Renderer();
echo $renderer->setType()->setTemplate('header')
  ->render();