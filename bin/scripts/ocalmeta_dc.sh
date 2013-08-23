#!/bin/bash
xmlstarlet ed -L -s '/svg:svg/svg:metadata/rdf:RDF/cc:Work' -t elem -n dc:$2 -v "$3" "$1"
