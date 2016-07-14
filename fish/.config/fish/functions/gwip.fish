function gwip
	git add -A; git ls-files --deleted -z | xargs -r0 git rm; git commit -m "--wip--" $argv;
end
