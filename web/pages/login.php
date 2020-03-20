<?php

$renderer = \Nick::Renderer();
echo $renderer->setType()->setTemplate('login')
  ->render();