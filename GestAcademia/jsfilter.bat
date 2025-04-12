@echo off
type %1 | sed "s/^\/\*\*/\/**\n/"