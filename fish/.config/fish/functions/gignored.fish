function gignored
	git ls-files -v | grep "^[[:lower:]]" $argv;
end
