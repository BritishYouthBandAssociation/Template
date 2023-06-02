<?php

enum ResourceTypes: int{
	case IMAGE =    0b00000001;
	case STRING =   0b00000010;

	case ARRAY =    0b01000000;
	case OPTIONAL = 0b10000000; 
}