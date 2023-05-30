<?php

enum ResourceTypes: int{
	case IMAGE = 0b0001;
	case STRING = 0b0010;

	case OPTIONAL = 0b10000000; 
}