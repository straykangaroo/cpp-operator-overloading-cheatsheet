all:    README.md


README.md:  index.html
	./extract_body.php index.html > README.md


.PHONY: clean
clean:
	rm README.md
