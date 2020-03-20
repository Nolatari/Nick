<?php

$renderer = \Nick::Renderer();
echo $renderer->setType()->setTemplate('footer')
  ->render();