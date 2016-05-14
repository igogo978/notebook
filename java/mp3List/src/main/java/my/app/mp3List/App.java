/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package my.app.mp3List;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FilenameFilter;
import java.io.IOException;
import java.io.RandomAccessFile;
import java.util.Arrays;
import java.util.Collections;
import java.util.List;
import java.util.Random;

/**
 *
 * @author Mary
 */
public class App {

    public static void main(String[] args) throws FileNotFoundException, IOException {
        if (args.length > 0) {
            System.out.println(args[0]);
            //String dirPath = "d:\\mp3\\oz";
            String dirPath = args[0];
            //過濾副檔名mp3
            FilenameFilter mp3Filter = new FilenameFilter() {
                @Override
                public boolean accept(File dir, String name) {
                    String lowercaseName = name.toLowerCase();
                    return lowercaseName.endsWith(".mp3");
                }
            };

            File folder = new File(dirPath);
            File[] listOfFiles = folder.listFiles(mp3Filter);

            List<File> asListOfFiles = Arrays.asList(listOfFiles);

            //打亂順序
            long seed = System.nanoTime();
            Collections.shuffle(asListOfFiles, new Random(seed));

            // create new file
            String file = dirPath + "\\playlist.txt";
            RandomAccessFile raf = new RandomAccessFile(file, "rw");

            if (listOfFiles.length > 0) {
                for (File listOfFile : asListOfFiles) {
                    if (listOfFile.isFile()) {

                        raf.write(listOfFile.toString().getBytes());
                        raf.writeUTF("\n");
                        System.out.println(listOfFile.getName());
                    } else if (listOfFile.isDirectory()) {
                        System.out.println("Directory " + listOfFile.getName());
                    }
                }
            }

        } else {
            System.out.println("Please add  a directory path  to filter mp3 files.\n");
        }//end if   

    }
} //end class App
