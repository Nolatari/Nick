<?php

$renderer = \Nick::Renderer();
echo $renderer->setType()->setTemplate('dashboard')
  ->render();